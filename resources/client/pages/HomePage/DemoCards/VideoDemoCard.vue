<template>
  <FlippableCard :front="front" :back="back" initialSide="back" />
</template>

<script setup lang="ts">
import { CardSide } from "@/types";
import FlippableCard from "@/components/FlippableCard.vue";

const back: CardSide = [
  {
    id: crypto.randomUUID(),
    type: "embed",
    content: "https://youtu.be/KTzGBJPuJwM?si=Fk-ky3lDDZCQvhJp",
    // content: "https://www.youtube.com/embed/KTzGBJPuJwM?si=Fk-ky3lDDZCQvhJp",
    meta: null,
  },
  // {
  //   id: crypto.randomUUID(),
  //   type: "text",
  //   content: "Title, Artist, Year?",
  //   meta: null,
  // },
];

const front: CardSide = [
  {
    id: crypto.randomUUID(),
    type: "text",
    content: `
      <p>How does the index of refraction arise?</p> <p>Why it depends on color?</p>
    `,
    meta: null,
  },
];
</script>

<style scoped></style>
