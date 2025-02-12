<template>
  <FlippableCard initialSideName="back" class="w-full">
    <template #front>
      <CardSideView :side="front" class="my-auto" />
    </template>

    <template #back>
      <CardSideView :side="back" class="my-auto" />
    </template>
  </FlippableCard>
</template>

<script setup lang="ts">
import { CardSide } from "@/types";
import CardSideView from "@/components/CardSideView/CardSideView.vue";
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
