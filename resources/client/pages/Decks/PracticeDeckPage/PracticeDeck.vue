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

      <div class="my-8">
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
  // if the score is 1, insert it somewhere in the front 1/2
  // if the score is 2, insert it somewhere in the back 1/2
  const startIndex = Math.floor(((score - 1) / 2) * length);
  const endIndex = startIndex + Math.floor(length / 2) - 1;
  const reinsertIndex = getRandomIntInclusive(startIndex, endIndex);

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

function initPracticeSession() {
  state.isTransitiongToNext = true;

  state.cardsToPractice = toShuffled(props.deck.cards);
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
