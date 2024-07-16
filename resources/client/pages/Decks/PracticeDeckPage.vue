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
      <Button
        asChild
        variant="secondary"
      >
        <RouterLink
          :to="{ name: 'decks.show', params: { deckId: props.deckId } }"
          class="flex gap-2 items-center"
        >
          End Practice
        </RouterLink>
      </Button>
    </PageHeader>

    <main>
      <div
        v-if="totalCards < 2"
        class="text-center"
      >
        <p>You need at least 2 cards to practice.</p>
        <Button
          class="mt-4"
          asChild
        >
          <RouterLink :to="`/decks/${deckId}/cards/create`"> Add a Card </RouterLink>
        </Button>
      </div>
      <div
        v-else-if="!state.activeCard"
        class="text-center"
      >
        <p>You have completed this practice session.</p>
        <Button
          @click="initPracticeSession"
          class="my-4"
        >
          Practice Again
        </Button>
      </div>

      <div v-else>
        <PracticeCardSide
          v-if="activeCardSide"
          :side="activeCardSide"
          class="h-[50dvh] portrait:aspect-[2/3] landscape:aspect-[3/2] mx-auto"
          :class="{
            'bg-black/50 text-white': state.activeSideName === 'back',
          }"
          @click="toggleActiveSide"
          :cardLabel="state.activeSideName"
        />

        <div
          v-if="activeCardHint"
          class="flex flex-col gap-1 items-center justify-center my-2"
        >
          <Button
            variant="ghost"
            class="mx-auto"
            @click="state.isShowingHint = !state.isShowingHint"
          >
            Hint
          </Button>
          <small v-if="state.isShowingHint && activeCardHint">
            {{ activeCardHint }}
          </small>
        </div>

        <div class="my-8">
          <CardAttemptChoices
            @answer="handleAnswer"
            :card="state.activeCard"
          />
          <CardAttemptsSummary :card="state.activeCard" />
        </div>

        <RouterLink :to="`/decks/${deckId}/practice/summary`">End Practice Session</RouterLink>
      </div>
    </main>
  </AuthenticatedLayout>
</template>
<script setup lang="ts">
import { ref, computed, watch, reactive } from 'vue';
import { AuthenticatedLayout } from '@/layouts/AuthenticatedLayout';
import { useDeckByIdQuery } from '@/queries/decks';
import * as T from '@/types';
import PracticeCardSide from '@/components/PracticeCardSide.vue';
import CardAttemptsSummary from '@/components/CardAttemptsSummary.vue';
import CardAttemptChoices from '@/components/CardAttemptChoices.vue';
import { Button } from '@/components/ui/button';
import PageHeader from '@/components/PageHeader.vue';

const props = defineProps<{
  deckId: number;
}>();

const deckIdRef = computed(() => props.deckId);

const { data: deck } = useDeckByIdQuery(deckIdRef);

const cards = computed(() => deck.value?.cards ?? []);
const totalCards = computed(() => cards.value.length);

type SideName = 'front' | 'back';

const state = reactive({
  activeCard: null as T.Card | null,
  initialSideName: 'front' as SideName,
  activeSideName: 'front' as SideName,
  cardsToPractice: [] as T.Card[],
  isShowingHint: false,
});

const activeCardSide = computed(() => state.activeCard?.[state.activeSideName] ?? null);

const activeCardHint = computed(() => {
  const hintSide = getOppositeSideName(state.initialSideName);
  return state.activeCard?.[hintSide].meta.hint ?? null;
});

function getOppositeSideName(sideName: SideName): SideName {
  return sideName === 'front' ? 'back' : 'front';
}

function toggleActiveSide() {
  state.activeSideName = getOppositeSideName(state.activeSideName);
}

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
    throw new Error('Cannot record score for a card that does not exist');
  }

  if (score === 3) {
    // remove the card from the deck
    state.cardsToPractice = state.cardsToPractice.filter(
      (card) => card.id !== state.activeCard?.id
    );
  } else {
    // probably should do something more sophisticated here
    const reinsertIndex = getFuzzyReinsertIndex(score, state.cardsToPractice.length);
    state.cardsToPractice.splice(reinsertIndex, 0, state.activeCard);
  }

  // then advance to the next card
  state.activeCard = state.cardsToPractice.shift() ?? null;
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
