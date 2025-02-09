<template>
  <div class="flex flex-col items-center justify-center">
    <audio :src="src" class="hidden" ref="audioRef" />
    <!-- show time -->
    <p v-if="audioRef" class="text-xs">
      {{ formatDuration(currentTime) }} / {{ formatDuration(duration) }}
    </p>
    <p v-else>Audio not loaded</p>
    <button
      @click.prevent.stop="playing = !playing"
      class="px-4 py-1 rounded-sm"
      :class="{
        'bg-brand-teal-300': playing,
        'bg-brand-maroon-900/5': !playing,
      }"
    >
      <IconCirclePause v-if="playing" />
      <IconCirclePlay v-else />
    </button>
  </div>
</template>
<script setup lang="ts">
import { computed, ref, watch } from "vue";
import { IconCirclePlay, IconCirclePause } from "./icons";
import { useMediaControls } from "@vueuse/core";

const props = defineProps<{
  src: string;
  isPlaying: boolean;
}>();

const audioRef = ref<HTMLAudioElement | null>(null);

const { playing, currentTime, duration } = useMediaControls(audioRef);

function formatDuration(seconds: number) {
  return new Date(1000 * seconds).toISOString().slice(15, 19);
}

watch(
  () => props.isPlaying,
  (isPlaying) => {
    // update playing state if we receive new value from parent
    playing.value = isPlaying;

    // reset time to 0 if we've stopped playing
    if (!isPlaying) {
      currentTime.value = 0;
    }
  },
);
</script>
<style scoped></style>
