<template>
  <div class="gif-player relative flex">
    <button
      @click="isPlaying = !isPlaying"
      class="absolute top-2 right-2 z-10 rounded-full text-brand-oatmeal-50"
      :class="{
        'bg-brand-maroon-500': isPlaying,
        'bg-brand-maroon-800': !isPlaying,
      }"
    >
      <span class="sr-only">{{ isPlaying ? "Pause" : "Play" }}</span>
      <IconCirclePause v-if="isPlaying" class="!size-8" />
      <IconCirclePlay v-else class="!size-8" />
    </button>
    <div
      class="absolute inset-0 z-0 bg-black"
      :class="{
        'opacity-0': isPlaying,
        'opacity-25': !isPlaying,
      }"
    ></div>
    <img v-show="isPlaying" :src="src" :alt="alt" class="gif-player__gif" />
    <img
      v-show="!isPlaying"
      :src="thumbSrc"
      :alt="alt"
      class="gif-player__thumb"
    />
  </div>
</template>
<script setup lang="ts">
import { ref } from "vue";
import { IconCirclePause, IconCirclePlay } from "./icons";

const props = withDefaults(
  defineProps<{
    thumbSrc: string;
    src: string;
    alt: string;
    isPlaying?: boolean;
  }>(),
  {
    isPlaying: false,
  },
);

const isPlaying = ref(props.isPlaying);
</script>
<style scoped>
img {
  max-width: 100%;
  display: block;
}
</style>
