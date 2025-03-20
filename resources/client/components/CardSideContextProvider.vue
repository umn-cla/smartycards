<template>
  <slot></slot>
</template>
<script setup lang="ts">
import * as T from "@/types";
import { provideCardSideContext } from "@/composables/useCardSideContext";

const props = defineProps<{
  deck: T.Deck;
  cardSideName: T.CardSideName;
}>();

provideCardSideContext(
  () => props.deck,
  () => props.cardSideName,
);
</script>
<style scoped></style>
