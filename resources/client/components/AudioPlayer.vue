<template>
  <div class="">
    <audio :src="src" controls class="max-w-full" ref="audioRef"></audio>
  </div>
</template>
<script setup lang="ts">
import { useIntersectionObserver } from "@vueuse/core";
import { ref } from "vue";

defineProps<{
  src: string;
}>();

const audioRef = ref<HTMLAudioElement | null>(null);

useIntersectionObserver(
  audioRef,
  ([{ isIntersecting }]) => {
    if (isIntersecting) {
      return;
    }

    // pause if scrolled out of view
    audioRef.value?.pause();
  },
  {
    threshold: 0.5,
  },
);
</script>
<style scoped></style>
