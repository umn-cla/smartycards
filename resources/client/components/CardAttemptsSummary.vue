<template>
  <dl class="text-sm">
    <dt>Attempts</dt>
    <dd>{{ cardStats ? cardStats.attempts_count : '-' }}</dd>
    <dt>Avg Score</dt>
    <dd>{{ cardStats ? cardStats.avg_score : '-' }}</dd>
  </dl>
</template>
<script setup lang="ts">
import * as T from '@/types';

defineProps<{
  cardStats: T.CardStats | null;
}>();
</script>
<style scoped></style>
