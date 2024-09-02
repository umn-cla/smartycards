<template>
  <div class="text-xs">
    <span v-if="score > 0.9">✅</span>
    <span v-else-if="score > 0.7">👍</span>
    <span v-else-if="score > 0.5">🫤</span>
    <span v-else-if="score > 0.3">😳</span>
    <span v-else>❌</span>
  </div>
</template>

<script setup lang="ts">
import { computed } from "vue";
import { IconCircleFilled, IconCircleOutline } from "./icons";
const props = withDefaults(
  defineProps<{
    score: number; // 0-1
    dots?: number;
  }>(),
  {
    dots: 5,
  },
);

const filledDots = computed(() => Math.ceil(props.score * props.dots));
const emptyDots = computed(() => props.dots - filledDots.value);
</script>

<style scoped></style>
