<template>
  <div class="relative">
    <!-- <Transition name="fade">
      <aside
        v-if="gameState !== 'start'"
        class="absolute inset-4 z-20 rounded-md text-black font-bold text-4xl backdrop-blur-sm px-4 py-3 flex items-center justify-center"
        :class="{
          'bg-brand-gold-500/75': gameState === 'match',
          'bg-brand-orange-500/50': gameState === 'mismatch',
          'bg-brand-teal-300/75': gameState === 'win',
        }"
      >
        <span v-if="gameState === 'match'">Match!</span>
        <span v-else-if="gameState === 'mismatch'">Not a match. Try again</span>
        <span v-else-if="gameState === 'win'">You win!</span>
      </aside>
    </Transition> -->
    <div class="matching-game grid grid-cols-4 gap-1">
      <TransitionGroup name="list">
        <MatchingSide
          v-for="side in shuffledSides"
          :blocks="side.blocks"
          :key="side.id"
          :label="side.label"
          :status="getSideStatus(side)"
          class="cursor-pointer"
          @click="handleClickSide(side)"
        />
      </TransitionGroup>
    </div>
  </div>
</template>
<script setup lang="ts">
import { toShuffled } from "@/lib/utils";
import * as T from "@/types";
import { computed, ref, watch } from "vue";
import MatchingSide from "./MatchingSide.vue";

interface CardSideWithId {
  id: string;
  cardId: T.Card["id"];
  label: T.CardSideName;
  blocks: T.CardSide;
}

const props = defineProps<{
  cards: T.Card[];
}>();

const emit = defineEmits<{
  (eventName: "gameover"): void;
}>();

const shuffledSides = ref<CardSideWithId[]>([]);
const selectedSides = ref<Set<CardSideWithId>>(new Set());
const gameState = ref<"start" | "match" | "mismatch" | "win">("start");

function isSelectedSide(side: CardSideWithId) {
  return selectedSides.value.has(side);
}

function doSidesMatch(side1: CardSideWithId, side2: CardSideWithId) {
  return side1.cardId === side2.cardId;
}

function getSideStatus(side: CardSideWithId) {
  if (gameState.value === "match" && selectedSides.value.has(side)) {
    return "correct";
  }

  if (gameState.value === "mismatch" && selectedSides.value.has(side)) {
    return "incorrect";
  }

  if (selectedSides.value.has(side)) {
    return "selected";
  }

  return "idle";
}

function handleMatch(side1: CardSideWithId, side2: CardSideWithId) {
  const weWon = shuffledSides.value.length === 2;
  gameState.value = weWon ? "win" : "match";

  // handle end turn after a bit
  setTimeout(() => {
    // remove matched sides from shuffled sides
    shuffledSides.value = shuffledSides.value.filter(
      (side) => side.id !== side1.id && side.id !== side2.id,
    );

    // reset selected sides
    selectedSides.value.clear();

    // reset state
    weWon ? emit("gameover") : (gameState.value = "start");
  }, 1000);
}

function handleMismatch() {
  gameState.value = "mismatch";

  setTimeout(() => {
    selectedSides.value.clear();
    gameState.value = "start";
  }, 1000);
}

function handleClickSide(side: CardSideWithId) {
  // if we've already selected this side, deselect it
  if (selectedSides.value.has(side)) {
    selectedSides.value.delete(side);
    return;
  }

  selectedSides.value.add(side);

  // if we're not at 2 sides, then we're done
  if (selectedSides.value.size < 2) {
    return;
  }

  // otherwise, handle the pair
  const [side1, side2] = Array.from(selectedSides.value);
  doSidesMatch(side1, side2) ? handleMatch(side1, side2) : handleMismatch();
}

watch(
  () => props.cards,
  () => {
    const sidesWithId = props.cards.reduce((acc, card): CardSideWithId[] => {
      const front = {
        id: `${card.id}-front`,
        cardId: card.id,
        label: "front" as const,
        blocks: card.front,
      };

      const back = {
        id: `${card.id}-back`,
        cardId: card.id,
        label: "back" as const,
        blocks: card.back,
      };

      return [...acc, front, back];
    }, [] as CardSideWithId[]);

    shuffledSides.value = toShuffled(sidesWithId);
  },
  { immediate: true },
);
</script>
<style scoped></style>
