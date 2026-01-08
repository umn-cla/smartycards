<template>
  <div>
    <div
      v-if="!state.activeCard"
      class="flex flex-col items-center justify-center py-12 bg-brand-oatmeal-50 rounded-md shadow-sm"
    >
      <p>You have completed this practice session.</p>
      <Button @click="initPracticeSession" class="my-4">
        Practice Again
      </Button>
      <Button asChild variant="secondary">
        <RouterLink
          :to="{ name: 'decks.show', params: { deckId: props.deck.id } }"
          class="flex gap-2 items-center"
        >
          End Practice
        </RouterLink>
      </Button>
    </div>
    <div v-else class="overflow-hidden">
      <FlippableCard
        :front="state.isTransitiongToNext ? [] : state.activeCard?.front"
        :back="state.isTransitiongToNext ? [] : state.activeCard?.back"
        :showLabels="true"
        :initialSideName="getInitialSideName(state.activeCard)"
        :deck="deck"
        class="max-w-screen-sm h-[50dvh] mx-auto transition-all duration-300"
        :class="{
          'opacity-0 translate-y-[50vh]': state.isTransitiongToNext,
          'opacity-100': !state.isTransitiongToNext,
        }"
      />
      <CardStackVisualization
        :total-cards="cardsRemaining + (state.activeCard ? 1 : 0)"
        :animation-state="state.stackAnimationState"
        :reinsertion-index="state.lastReinsertionIndex"
      />
      <div class="my-4 sm:my-8">
        <CardAttemptChoices
          :disabled="state.stackAnimationState !== 'idle'"
          @answer="handleAnswer"
          :initialSideName="getInitialSideName(state.activeCard)"
          :card="state.activeCard"
          class="relative z-10"
        />
      </div>
    </div>
  </div>
</template>
<script setup lang="ts">
import * as T from "@/types";
import CardAttemptChoices from "@/components/CardAttemptChoices.vue";
import CardStackVisualization from "@/components/CardStackVisualization.vue";
import FlippableCard from "@/components/FlippableCard.vue";
import { Button } from "@/components/ui/button";
import { reactive, watch, onMounted, computed } from "vue";
import { toShuffled, getRandomIntInclusive } from "@/lib/utils";
import { partition } from "ramda";

const props = defineProps<{
  deck: T.DeckWithCards;
  initialSideName: T.CardSideName | "random";
}>();

const emit = defineEmits<{
  (eventName: "complete", cardCount: number): void;
  (eventName: "init"): void;
}>();

const state = reactive({
  cardsToPractice: [] as T.Card[],
  activeCard: null as T.Card | null,
  isTransitiongToNext: false,

  // we want "sticky" random sides for each card
  // so that the user sees the same side when it comes up again
  randomSideMap: {} as Record<T.Card["id"], T.CardSideName>,

  // Stack animation state
  stackAnimationState: "idle" as "idle" | "removing" | "reinserting",
  lastReinsertionIndex: null as number | null,
});

const cardsRemaining = computed(() => state.cardsToPractice.length);

function getInitialSideName(card: T.Card): T.CardSideName {
  return props.initialSideName === "random"
    ? getRandomSideForCard(card.id)
    : props.initialSideName;
}

function getFuzzyReinsertIndex(score: number, length: number): number {
  if (score < 1 || score > 3) {
    throw new Error("Invalid score");
  }

  if (length <= 2) {
    return 1;
  }

  const SMALL_DECK_THRESHOLD = 10;
  const frontHalfEndIndex = Math.floor(length / 2);

  // for small decks, let's just reinsert them in the front/back
  // halves
  if (length <= SMALL_DECK_THRESHOLD) {
    return score === 1
      ? getRandomIntInclusive(1, frontHalfEndIndex)
      : getRandomIntInclusive(frontHalfEndIndex + 1, length - 1);
  }

  // otherwise, 1's should go in the next few cards
  if (score === 1) {
    return getRandomIntInclusive(1, 5);
  }

  if (score === 2) {
    return getRandomIntInclusive(6, 10);
  }

  console.error(
    "getFuzzyReinserIndex: we shouldnt be here. randomly reinserting",
  );

  return getRandomIntInclusive(1, length - 1);
}

function handleAnswer(score: number) {
  if (!state.activeCard) {
    throw new Error("Cannot record score for a card that does not exist");
  }

  // Capture the current card for use in setTimeout callbacks
  const currentCard = state.activeCard;

  if (score === 3) {
    // Trigger puff animation
    state.stackAnimationState = "removing";

    setTimeout(() => {
      state.cardsToPractice = state.cardsToPractice.filter(
        (card) => card.id !== currentCard.id,
      );
      state.stackAnimationState = "idle";
    }, 300); // Match puff animation duration
  } else {
    // Trigger shuffle animation
    state.stackAnimationState = "reinserting";

    const reinsertIndex = getFuzzyReinsertIndex(
      score,
      state.cardsToPractice.length,
    );

    state.cardsToPractice.splice(reinsertIndex, 0, currentCard);

    setTimeout(() => {
      state.stackAnimationState = "idle";
    }, 400); // Match shuffle animation duration
  }

  // reset the side
  state.isTransitiongToNext = true;

  // then advance to the next card
  state.activeCard = state.cardsToPractice.shift() ?? null;

  // if there is no active card, we've completed the session
  if (!state.activeCard) {
    emit("complete", props.deck.cards.length);
  }

  // after animation is complete, show the initial side
  setTimeout(() => {
    state.isTransitiongToNext = false;
  }, 500);
}

function getRandomSide(): T.CardSideName {
  return Math.random() < 0.5 ? "front" : "back";
}

function getRandomSideForCard(cardId: T.Card["id"]): T.CardSideName {
  const randomSide = state.randomSideMap[cardId] ?? getRandomSide();
  state.randomSideMap[cardId] = randomSide;
  return randomSide;
}

// first sorts the cards by score
// then break the deck into 4 parts and shuffle each
function toPartitionedShuffle(cards: T.Card[]): T.Card[] {
  // partition into groups by score
  const [group1, leftovers1] = partition(
    (card) => (card.avg_score ?? 0) <= 1.5,
    cards,
  );
  const [group2, leftovers2] = partition(
    (card) => (card.avg_score ?? 0) > 1.5 && (card.avg_score ?? 0) <= 2.0,
    leftovers1,
  );
  const [group3, group4] = partition(
    (card) => (card.avg_score ?? 0) > 2.0 && (card.avg_score ?? 0) <= 2.5,
    leftovers2,
  );

  // return shuffled groups
  return [
    ...toShuffled(group1),
    ...toShuffled(group2),
    ...toShuffled(group3),
    ...toShuffled(group4),
  ];
}

function initPracticeSession() {
  emit("init");

  state.isTransitiongToNext = true;

  state.cardsToPractice = toPartitionedShuffle(props.deck.cards);
  state.activeCard = state.cardsToPractice.shift() ?? null;
  state.randomSideMap = {};
  setTimeout(() => {
    state.isTransitiongToNext = false;
  }, 500);
}

onMounted(() => {
  initPracticeSession();
});

watch(
  () => props.initialSideName,
  () => {
    // reinit the practice session
    // if the side changes
    initPracticeSession();
  },
  { immediate: true },
);
</script>
<style scoped></style>
