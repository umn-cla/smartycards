<template>
  <AuthenticatedLayout>
    <PageHeader
      v-if="deck"
      :title="'Practice'"
      :subtitle="deck.name"
      :backLabel="deck.name"
      :backTo="{ name: 'decks.show', params: { deckId: deckId } }"
      size="xs"
    >
      <Button asChild variant="secondary">
        <RouterLink
          :to="{ name: 'decks.show', params: { deckId: props.deckId } }"
          class="flex gap-2 items-center"
        >
          End Practice
        </RouterLink>
      </Button>
    </PageHeader>

    <main>
      <Transition name="fade" mode="out-in">
        <div v-if="isDeckLoading" class="text-center">...</div>
        <div v-else-if="totalCards < 2" class="text-center">
          <p>You need at least 2 cards to practice.</p>
          <Button class="mt-4" asChild>
            <RouterLink :to="`/decks/${deckId}/cards/create`">
              Add a Card
            </RouterLink>
          </Button>
        </div>
        <div v-else-if="!state.activeCard" class="text-center">
          <p>You have completed this practice session.</p>
          <Button @click="initPracticeSession" class="my-4">
            Practice Again
          </Button>
        </div>

        <div v-else class="overflow-hidden">
          <FlippableCard
            :front="state.isTransitiongToNext ? [] : state.activeCard?.front"
            :back="state.isTransitiongToNext ? [] : state.activeCard?.back"
            :initialSide="state.initialSideName"
            class="w-60 mx-auto transition-all duration-500"
            :class="{
              'opacity-0 translate-x-[100vw]': state.isTransitiongToNext,
              'opacity-100': !state.isTransitiongToNext,
            }"
          />

          <div class="my-8">
            <CardAttemptChoices
              @answer="handleAnswer"
              :card="state.activeCard"
            />
            <CardAttemptsSummary
              v-if="activeCardWithStats"
              :cardStats="activeCardWithStats"
            />
          </div>
        </div>
      </Transition>
    </main>
  </AuthenticatedLayout>
</template>
<script setup lang="ts">
import { computed, watch, reactive } from "vue";
import { AuthenticatedLayout } from "@/layouts/AuthenticatedLayout";
import { useDeckByIdQuery } from "@/queries/decks";
import * as T from "@/types";
import { CardSideView } from "@/components/CardSideView";
import CardAttemptsSummary from "@/components/CardAttemptsSummary.vue";
import CardAttemptChoices from "@/components/CardAttemptChoices.vue";
import { Button } from "@/components/ui/button";
import PageHeader from "@/components/PageHeader.vue";
import { useCardStatsByIdQuery } from "@/queries/cards/useCardStatsByIdQuery";
import FlippableCard from "@/components/FlippableCard.vue";

const props = defineProps<{
  deckId: number;
}>();

const state = reactive({
  activeCard: null as T.Card | null,
  initialSideName: "front" as SideName,
  activeSideName: "front" as SideName,
  cardsToPractice: [] as T.Card[],
  isShowingHint: false,
  isTransitiongToNext: false,
});

const deckIdRef = computed(() => props.deckId);
const activeCardId = computed(() => state.activeCard?.id ?? null);

const { data: deck, isLoading: isDeckLoading } = useDeckByIdQuery(deckIdRef);
const { data: activeCardWithStats } = useCardStatsByIdQuery(activeCardId);

const cards = computed(() => deck.value?.cards ?? []);
const totalCards = computed(() => cards.value.length);

type SideName = "front" | "back";

function shuffleCards(cards: T.Card[]): T.Card[] {
  return [...cards].sort(() => Math.random() - 0.5);
}

function getRandomIntBetweenInclusive(min: number, max: number): number {
  return Math.floor(Math.random() * (max - min + 1) + min);
}

function getFuzzyReinsertIndex(score: number, length: number): number {
  // if the score is 1, insert it somewhere in the front 1/2
  // if the score is 2, insert it somewhere in the back 1/2
  const startIndex = Math.floor(((score - 1) / 2) * length);
  const endIndex = startIndex + Math.floor(length / 2) - 1;
  const reinsertIndex = getRandomIntBetweenInclusive(startIndex, endIndex);

  // if the reinsert index is 0, then return 1 so that it's not the next card
  return reinsertIndex === 0 ? 1 : reinsertIndex;
}

function handleAnswer(score: number) {
  if (!state.activeCard) {
    throw new Error("Cannot record score for a card that does not exist");
  }

  if (score === 3) {
    // remove the card from the deck
    state.cardsToPractice = state.cardsToPractice.filter(
      (card) => card.id !== state.activeCard?.id,
    );
  } else {
    // probably should do something more sophisticated here
    const reinsertIndex = getFuzzyReinsertIndex(
      score,
      state.cardsToPractice.length,
    );
    state.cardsToPractice.splice(reinsertIndex, 0, state.activeCard);
  }

  // reset the side
  state.isTransitiongToNext = true;

  // then advance to the next card
  state.activeCard = state.cardsToPractice.shift() ?? null;

  // after animation is complete, show the initial side
  setTimeout(() => {
    state.isTransitiongToNext = false;
  }, 500);
}

function initPracticeSession() {
  if (!deck.value) return;
  state.cardsToPractice = shuffleCards(cards.value);
  state.activeCard = state.cardsToPractice.shift() ?? null;
}

watch(deck, () => initPracticeSession(), { immediate: true });
</script>
<style scoped>
button {
  &:hover {
    text-decoration: none;
  }
}
</style>
