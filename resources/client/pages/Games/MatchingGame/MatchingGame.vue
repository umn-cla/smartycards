<template>
  <div class="matching-game grid grid-cols-4 gap-1">
    <MatchingSide
      v-for="side in shuffledSides"
      :blocks="side.blocks"
      :key="side.id"
      :label="side.label"
    />
  </div>
</template>
<script setup lang="ts">
import CardThumbnail from "@/CardThumbnail.vue";
import { toShuffled } from "@/lib/utils";
import * as T from "@/types";
import { computed, ref, watch } from "vue";
import MatchingSide from "./MatchingSide.vue";

const props = defineProps<{
  cards: T.Card[];
}>();

const shuffledSides = ref<CardSideWithId[]>([]);

interface CardSideWithId {
  id: string;
  label: T.CardSideName;
  blocks: T.CardSide;
}

watch(
  () => props.cards,
  () => {
    const sidesWithId = props.cards.reduce((acc, card): CardSideWithId[] => {
      const front = {
        id: card.id + "-front",
        // use `as const` so that the type is not a generic string type,
        // but specifically "front"
        label: "front" as const,
        blocks: card.front,
      };

      const back = {
        id: card.id + "-back",
        label: "back" as const, // see above comment for front
        blocks: card.back,
      };

      return [...acc, front, back];
    }, [] as CardSideWithId[]);

    shuffledSides.value = toShuffled(sidesWithId);
  },
  { immediate: true },
);

defineEmits<{
  (eventName: "gameover"): void;
}>();
</script>
<style scoped></style>
