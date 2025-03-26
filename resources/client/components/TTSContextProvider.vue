<template>
  <slot></slot>
</template>
<script setup lang="ts">
import * as T from "@/types";
import { provideTTSContext } from "@/composables/useTTSContext";

const props = defineProps<{
  deck: T.Deck | null;
  cardSideName: T.CardSideName;
}>();

provideTTSContext(
  () => props.deck,
  () => props.cardSideName,
);
</script>
<style scoped></style>
