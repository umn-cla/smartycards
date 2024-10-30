<template>
  <div>
    <div v-if="!state.activeCard" class="text-center">
      <p>You have completed this practice session.</p>
      <Button @click="initPracticeSession" class="my-4">
        Practice Again
      </Button>
    </div>
    <div v-else class="overflow-hidden">
      <FlippableCard
        :front="state.isTransitiongToNext ? [] : state.activeCard?.front"
        :back="state.isTransitiongToNext ? [] : state.activeCard?.back"
        :showLabels="true"
        :initialSideName="
          initialSideName === 'random'
            ? getRandomSideForCard(state.activeCard?.id)
            : initialSideName
        "
        class="max-w-full max-h-full mx-auto transition-all duration-300"
        :class="{
          'w-[33dvh] h-[50dvh]': orientation === 'portrait',
          'w-[50dvw] h-[33dvw]': orientation === 'landscape',
          'opacity-0 translate-y-[50vh]': state.isTransitiongToNext,
          'opacity-100': !state.isTransitiongToNext,
        }"
      />

      <div class="my-4 sm:my-8">
        <CardAttemptChoices
          @answer="handleAnswer"
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
import FlippableCard from "@/components/FlippableCard.vue";
import { Button } from "@/components/ui/button";
import { reactive, watch, onMounted } from "vue";
import { toShuffled, getRandomIntInclusive } from "@/lib/utils";
import { partition } from "ramda";

const props = defineProps<{
  deck: T.DeckWithCards;
  initialSideName: T.CardSideName | "random";
  orientation: "portrait" | "landscape";
}>();

const emit = defineEmits<{
  (eventName: "complete", cardCount: number): void;
}>();

const state = reactive({
  cardsToPractice: [] as T.Card[],
  activeCard: null as T.Card | null,
  isTransitiongToNext: false,

  // we want "sticky" random sides for each card
  // so that the user sees the same side when it comes up again
  randomSideMap: {} as Record<T.Card["id"], T.CardSideName>,
});

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
