<template>
  <svg
    version="1.1"
    xmlns="http://www.w3.org/2000/svg"
    xmlns:xlink="http://www.w3.org/1999/xlink"
    :viewBox="`0 0 ${(2 * circleRadius + 2) * totalCircles} ${2 * circleRadius}`"
    class="h-2"
  >
    <defs>
      <linearGradient id="linear-gradient">
        <stop offset="0%" stop-color="#D6AA9B" />
        <stop offset="90%" stop-color="#DE3B04" />
      </linearGradient>
      <clipPath id="circle-clip">
        <circle
          v-for="n in totalCircles"
          :cx="13 * 2 * (n - 1) + 13"
          cy="12"
          r="12"
        />
      </clipPath>
      <mask :id="`score-mask-${rectWidthPercent}`">
        <rect :width="`${rectWidthPercent}%`" height="100%" fill="white" />
      </mask>
    </defs>
    <rect
      width="100%"
      height="100%"
      fill="url(#linear-gradient)"
      clip-path="url(#circle-clip)"
      :mask="`url(#score-mask-${rectWidthPercent})`"
    />
  </svg>
</template>

<script setup lang="ts">
import { computed } from "vue";
const props = defineProps<{
  score: number;
}>();

const circleRadius = 12;
const totalCircles = 3;

const rectWidthPercent = computed(
  () => (Math.ceil(props.score) / totalCircles) * 100,
);
</script>

<style scoped></style>
