<template>
  <FlippableCard :front="front" :back="back" />
</template>

<script setup lang="ts">
import { CardSide } from "@/types";
import FlippableCard from "@/components/FlippableCard.vue";

const front: CardSide = [
  {
    id: crypto.randomUUID(),
    type: "audio",
    content: "https://www.allaboutbirds.org/guide/assets/sound/550633.mp3",
    meta: null,
  },
  // {
  //   id: crypto.randomUUID(),
  //   type: "text",
  //   content: "Which bird is singing?",
  //   meta: null,
  // },
];

const back: CardSide = [
  {
    id: crypto.randomUUID(),
    type: "image",
    content:
      "https://upload.wikimedia.org/wikipedia/commons/thumb/d/d9/Carduelis_tristis_-Michigan%2C_USA_-male-8.jpg/892px-Carduelis_tristis_-Michigan%2C_USA_-male-8.jpg",
    meta: {
      alt: "American Goldfinch, By Rodney Campbell - GoldfinchUploaded by snowmanradio, CC BY 2.0, https://commons.wikimedia.org/w/index.php?curid=20388809",
    },
  },
  {
    id: crypto.randomUUID(),
    type: "text",
    content: `
    <div class="text-center">
    <p class="text-2xl">American Goldfinch</p>
    <a href="https://en.wikipedia.org/wiki/American_goldfinch"
      target="_blank"
      rel="noopener noreferrer"
      class="text-brand-teal-500 inline-block mt-4"
    >
      Learn more
      </a>
    </div>
    `,
    meta: null,
  },
];
</script>

<style scoped></style>
