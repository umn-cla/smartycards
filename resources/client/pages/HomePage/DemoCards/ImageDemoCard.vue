<template>
  <FlippableCard :front="front" :back="back" />
</template>

<script setup lang="ts">
import twoFridasImgUrl from "@/assets/demo-cards/the-two-fridas.jpg";
import { CardSide } from "@/types";
import FlippableCard from "@/components/FlippableCard.vue";

const front: CardSide = [
  {
    id: crypto.randomUUID(),
    type: "image",
    content: twoFridasImgUrl,
    meta: {
      alt: "The Two Fridas by Frida Kahlo",
    },
  },
  // {
  //   id: crypto.randomUUID(),
  //   type: "text",
  //   content: "Title, Artist, Year?",
  //   meta: null,
  // },
];

const back: CardSide = [
  {
    id: crypto.randomUUID(),
    type: "text",
    content: `
    <div class="text-center">
    <b>The Two Fridas</b>
    <p>Frida Kahlo</p>
    <p>1939</p>
    <a href="https://en.wikipedia.org/wiki/The_Two_Fridas"
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
