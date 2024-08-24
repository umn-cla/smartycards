<template>
  <div class="flex justify-end items-center">
    <span class="text-xs border text-black/50 px-2 rounded-full">{{
      props.card.attempts_count
    }}</span>
    Difficulty
    <ScoreDots :score="3 - card.avg_score" />
  </div>
</template>
<script setup lang="ts">
import Tuple from "@/components/Tuple.vue";
import * as T from "@/types";
import { useTimeAgo } from "@vueuse/core";
import ScoreDots from "./ScoreDotsSvg.vue";

const props = defineProps<{
  card: T.Card;
}>();

const timeAgo = useTimeAgo(props.card.last_attempted_at);
</script>
<style scoped></style>
