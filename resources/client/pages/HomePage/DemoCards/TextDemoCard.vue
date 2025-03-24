<template>
  <FlippableCard :front="front" :back="back" initialSideName="back" />
</template>

<script setup lang="ts">
import { CardSide } from "@/types";
import FlippableCard from "@/components/FlippableCard.vue";

const back: CardSide = [
  {
    id: crypto.randomUUID(),
    type: "text",
    content: "<p>escuela</p>",
    meta: {
      lang: "es-MX",
    },
  },
];

const front: CardSide = [
  {
    id: crypto.randomUUID(),
    type: "text",
    content: `<p>school</p>`,
    meta: null,
  },
];
</script>

<style scoped></style>
