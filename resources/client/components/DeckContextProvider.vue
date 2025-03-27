<template>
  <slot></slot>
</template>
<script setup lang="ts">
import * as T from "@/types";
import { provideDeckContext } from "@/composables/useDeckContext";

const props = defineProps<{
  deck: T.Deck;
}>();

provideDeckContext(() => props.deck);
</script>
<style scoped></style>
