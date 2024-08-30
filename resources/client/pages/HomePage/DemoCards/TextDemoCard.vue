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
    content: "<p>Trường học</p>",
    meta: null,
  },
];

const front: CardSide = [
  {
    id: crypto.randomUUID(),
    type: "text",
    content: `
    <p>School</p>
    <small><i>Vietnamese</i></small>`,
    meta: null,
  },
];
</script>

<style scoped></style>
