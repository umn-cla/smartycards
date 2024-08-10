<template>
  <div class="flex flex-col gap-2 border rounded-lg py-2 px-4">
    <Tuple label="Last Practiced">{{ card.last_attempted_at ? lastPracticedTimeAgo : '-' }} </Tuple>
    <Tuple label="Avg Score">
      {{ card.avg_score ? Number(card.avg_score).toFixed(2) : '-' }}
    </Tuple>
  </div>
</template>
<script setup lang="ts">
import Tuple from '@/components/Tuple.vue';
import * as T from '@/types';
import { useTimeAgo } from '@vueuse/core';

const props = defineProps<{
  card: T.Card;
}>();

const lastPracticedTimeAgo = useTimeAgo(() => props.card.last_attempted_at);
</script>
<style scoped></style>
