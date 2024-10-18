<template>
  <div class="matching-game grid grid-cols-4 gap-1">
    <CardThumbnail
      v-for="side in shuffledSides"
      :side="side"
      :aspectRatio="'square'"
    />
  </div>
</template>
<script setup lang="ts">
import CardThumbnail from "@/CardThumbnail.vue";
import { toShuffled } from "@/lib/utils";
import * as T from "@/types";
import { computed } from "vue";

const props = defineProps<{
  cards: T.Card[];
}>();

const shuffledSides = computed((): T.CardSide[] => {
  const sides = props.cards.reduce((acc, card) => {
    acc.push(card.front, card.back);
    return acc;
  }, [] as T.CardSide[]);

  return toShuffled(sides);
});

defineEmits<{
  (eventName: "gameover"): void;
}>();
</script>
<style scoped></style>
