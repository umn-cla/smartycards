<template>
  <AuthenticatedLayout>
    <div class="mx-4">
      <h1 class="my-4">Practice Summary</h1>

      <p>Some stats about the latest practice session.</p>

      <div class="flex flex-col gap-2 my-8">
        <RouterLink :to="{ name: 'decks.show', params: { deckId } }">Back to Deck</RouterLink>
        <RouterLink :to="{ name: 'decks.index' }">All Decks</RouterLink>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
<script setup lang="ts">
import { AuthenticatedLayout } from '@/layouts/AuthenticatedLayout';
import { RouterLink } from 'vue-router';

defineProps<{
  deckId: number;
}>();
</script>
<style scoped></style>
