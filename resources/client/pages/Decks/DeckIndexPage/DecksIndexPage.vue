<template>
  <AuthenticatedLayout>
    <main>
      <PageHeader title="Decks" size="lg" />

      <section>
        <h3 class="text-3xl font-bold text-brand-maroon-800 mb-4">My Decks</h3>

        <div class="card-grid !gap-6 sm:gap-4">
          <RouterLink
            :to="{ name: 'decks.create' }"
            class="bg-brand-maroon-800/2 flex w-full h-full items-center justify-center rounded-xl flex-col gap-2 border-2 border-dashed border-black/10 px-4 py-8 hover:bg-brand-teal-300/10 transition-colors hover:text-brand-teal-500"
          >
            <IconPlusFilled class="w-6 h-6" />
            <span>Create Deck</span>
          </RouterLink>

          <DeckListItem :deck="deck" v-for="deck in myDecks" :key="deck.id" />
        </div>
      </section>

      <section class="my-8">
        <h3 class="text-3xl font-bold text-brand-maroon-800 mb-4">
          Shared Decks
        </h3>
        <div class="card-grid !gap-6 sm:gap-4" v-if="sharedDecks.length">
          <DeckListItem
            :deck="deck"
            v-for="deck in sharedDecks"
            :key="deck.id"
          />
        </div>
        <p v-else class="my-4">No shared decks</p>
      </section>
    </main>
  </AuthenticatedLayout>
</template>
<script setup lang="ts">
import { RouterLink } from "vue-router";
import { AuthenticatedLayout } from "@/layouts/AuthenticatedLayout";
import { useAllDecksQuery } from "@/queries/decks";
import DeckListItem from "./DeckListItem.vue";
import { computed } from "vue";
import * as T from "@/types";
import PageHeader from "@/components/PageHeader.vue";
import { IconPlusFilled } from "@/components/icons";

const { data: decks } = useAllDecksQuery();

const myDecks = computed((): T.Deck[] => {
  return (
    decks.value?.filter((deck) => deck.current_user_role === "owner") ?? []
  );
});

const sharedDecks = computed((): T.Deck[] => {
  return (
    decks.value?.filter((deck) => deck.current_user_role !== "owner") ?? []
  );
});
</script>
<style scoped></style>
