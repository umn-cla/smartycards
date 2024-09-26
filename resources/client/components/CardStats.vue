<template>
  <div class="flex justify-end items-center">
    <span class="text-xs border text-brand-maroon-800/50 px-2 rounded-full">{{
      props.card.attempts_count
    }}</span>
    Difficulty
    <ScoreDots :score="score" />
  </div>
</template>
<script setup lang="ts">
import * as T from "@/types";
import ScoreDots from "./ScoreEmoji.vue";
import { computed } from "vue";

const props = defineProps<{
  card: T.Card;
}>();

const score = computed(() => props.card.avg_score / 3);
</script>
<style scoped></style>
