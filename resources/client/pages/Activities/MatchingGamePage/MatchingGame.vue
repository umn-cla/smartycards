<template>
  <div>
    <div class="relative">
      <div
        v-if="gameState === 'win'"
        class="flex flex-col items-center justify-center p-4 border bg-brand-oatmeal-50 rounded-md gap-4"
      >
        <p class="text-center text-4xl font-bold">You win!</p>
        <Button ref="playAgainButton" @click="startNewGame()">
          Play Again
        </Button>
      </div>
      <div
        v-else-if="gameState === 'playing'"
        ref="sideGrid"
        class="matching-game grid grid-cols-4 gap-1"
      >
        <TransitionGroup name="list">
          <SelectableMatchingSide
            v-for="(side, index) in matchingGameStore.sides"
            :key="side.id"
            :side="side"
            :position="index + 1"
            @select="selectSideAndAnnounce(side.id)"
          />
        </TransitionGroup>
      </div>
      <div v-else>
        <p>Something went wrong. Please try again.</p>
        <Button @click="reload()">Reload page</Button>
      </div>
    </div>
  </div>
</template>
<script setup lang="ts">
import * as T from "@/types";
import { computed, nextTick, useTemplateRef, watch } from "vue";
import { useAnnouncer } from "@vue-a11y/announcer";
import SelectableMatchingSide from "./SelectableMatchingSide.vue";
import { SelectSideResult, useMatchingGameStore } from "./matchingGameStore";
import { Button } from "@/components/ui/button";

const props = defineProps<{
  cards: T.Card[];
}>();

const emit = defineEmits<{
  (eventName: "gameover", cardCount: number): void;
}>();

const matchingGameStore = useMatchingGameStore();
const gameState = computed(() => matchingGameStore.gameState);
const announcer = useAnnouncer();
const playAgainButton =
  useTemplateRef<InstanceType<typeof Button>>("playAgainButton");
const sideGrid = useTemplateRef<HTMLDivElement>("sideGrid");

function toPairAnnouncement(result: SelectSideResult): string | null {
  switch (result.type) {
    case "pairIncomplete":
      return null;
    case "pairMismatched":
      return "Not a match. Try again.";
    case "pairMatched":
      if (result.unmatchedPairCount === 0) {
        return "Match.";
      }
      if (result.unmatchedPairCount === 1) {
        return "Match. 1 pair left.";
      }
      return `Match. ${result.unmatchedPairCount} pairs left.`;
  }
}

function selectSideAndAnnounce(sideId: string): void {
  const selectSideResult = matchingGameStore.selectSide(sideId);
  const announcement = toPairAnnouncement(selectSideResult);
  if (announcement) {
    announcer.polite(announcement);
  }
}

function focusPlayAgainButton(): void {
  const playAgainElement: unknown = playAgainButton.value?.$el;
  if (playAgainElement instanceof HTMLButtonElement) {
    playAgainElement.focus();
  }
}

function focusFirstSide(): void {
  sideGrid.value
    ?.querySelector<HTMLButtonElement>("button[aria-pressed]")
    ?.focus();
}

async function startNewGame(): Promise<void> {
  matchingGameStore.init(props.cards);
  await nextTick();
  focusFirstSide();
}

function reload() {
  window.location.reload();
}

watch(
  () => props.cards,
  () => {
    matchingGameStore.init(props.cards);
  },
  { immediate: true },
);

watch(gameState, async (state) => {
  if (state !== "win") {
    return;
  }

  const matchedPairs = matchingGameStore.sides.length / 2;
  emit("gameover", matchedPairs);
  announcer.polite("You win!");

  await nextTick();
  focusPlayAgainButton();
});
</script>
<style scoped></style>
