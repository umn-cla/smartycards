<template>
  <div>
    <h2 class="text-center font-bold text-xl mb-4">Matching</h2>
    <div class="relative">
      <div
        v-if="gameState === 'win'"
        class="flex flex-col items-center justify-center p-4 border bg-brand-oatmeal-50 rounded-md gap-4"
      >
        <p class="text-center text-4xl font-bold">You win!</p>
        <Button @click="matchingGameStore.init(cards)"> Play Again </Button>
      </div>
      <div
        v-else-if="gameState === 'playing'"
        class="matching-game grid grid-cols-4 gap-1"
      >
        <TransitionGroup name="list">
          <MatchingSide
            v-for="side in matchingGameStore.sides"
            :blocks="side.blocks"
            :key="side.id"
            :label="side.label"
            :status="side.status"
            @click="matchingGameStore.selectSide(side.id)"
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
import { computed, reactive, ref, watch } from "vue";
import MatchingSide from "./MatchingSide.vue";
import { useMatchingGameStore } from "./matchingGameStore";
import { Button } from "@/components/ui/button";

const props = defineProps<{
  cards: T.Card[];
}>();

const emit = defineEmits<{
  (eventName: "gameover"): void;
}>();

const matchingGameStore = useMatchingGameStore();
const gameState = computed(() => matchingGameStore.gameState);

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
</script>
<style scoped></style>
