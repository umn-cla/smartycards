<template>
  <AuthenticatedLayout>
    <main class="max-w-screen-lg mx-auto">
      <PageHeader
        title="Decks"
        size="lg"
      >
        <Button
          :as="RouterLink"
          :to="{ name: 'decks.create' }"
          variant="secondary"
        >
          Create Deck
        </Button>
      </PageHeader>

      <section class="my-8">
        <h3 class="text-3xl font-bold text-black/25">My Decks</h3>
        <ul
          v-if="myDecks?.length"
          class="flex flex-col py-4 gap-1"
        >
          <DeckListItem
            v-for="deck in myDecks"
            :key="deck.id"
            :deck="deck"
          />
        </ul>
        <p
          v-else
          class="my-4"
        >
          No decks found
        </p>
      </section>

      <section>
        <h3 class="text-3xl font-bold text-black/25">Shared Decks</h3>
        <ul
          v-if="sharedDecks.length"
          class="flex flex-col py-4 gap-1"
        >
          <DeckListItem
            v-for="deck in sharedDecks"
            :key="deck.id"
            :deck="deck"
          />
        </ul>
        <p
          v-else
          class="my-4"
        >
          No shared decks.
        </p>
      </section>
    </main>
  </AuthenticatedLayout>
</template>
<script setup lang="ts">
import { RouterLink } from 'vue-router';
import { AuthenticatedLayout } from '@/layouts/AuthenticatedLayout';
import { useAllDecksQuery } from '@/queries/decks';
import { Button } from '@/components/ui/button';
import DeckListItem from './DeckListItem.vue';
import { computed } from 'vue';
import * as T from '@/types';
import PageHeader from '@/components/PageHeader.vue';

const { data: decks } = useAllDecksQuery();

const myDecks = computed((): T.Deck[] => {
  return decks.value?.filter((deck) => deck.current_user_role === 'owner') ?? [];
});

const sharedDecks = computed((): T.Deck[] => {
  return decks.value?.filter((deck) => deck.current_user_role !== 'owner') ?? [];
});
</script>
<style scoped></style>
