<template>
  <div class="">
    <div v-if="isPending">...</div>
    <div v-else-if="isError">{{ error }}</div>

    <dl v-else class="text-sm">
      <dt>Attempts</dt>
      <dd>{{ cardAttempts?.length ?? 0 }}</dd>
      <dt>Avg Score</dt>
      <dd>{{ avgScore ?? '-' }}</dd>
    </dl>
  </div>
</template>
<script setup lang="ts">
import { useCardAttemptsQuery } from '@/queries/cardAttempts';
import * as T from '@/types';
import { computed } from 'vue';

const props = defineProps<{
  card: T.Card;
}>();

const cardId = computed(() => props.card.id);

// NOTE: query needs a Ref to be reactive
const { data: cardAttempts, isPending, isError, error } = useCardAttemptsQuery(cardId);

const avgScore = computed(() => {
  if (!cardAttempts.value?.length) return null;

  const totalScore = cardAttempts.value.reduce((acc, attempt) => acc + attempt.score, 0);
  return (totalScore / cardAttempts.value.length).toFixed(2);
});
</script>
<style scoped></style>
