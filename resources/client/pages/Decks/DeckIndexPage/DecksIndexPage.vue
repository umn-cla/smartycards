<template>
  <AuthenticatedLayout>
    <main>
      <PageHeader title="Decks" size="lg" />

      <CardGrid title="My Decks" :items="myDecks" key="id">
        <template #prepend>
          <RouterLink
            :to="{ name: 'decks.create' }"
            class="bg-black/2 flex w-full h-full items-center justify-center rounded-xl flex-col gap-2 border-2 border-dashed border-black/10 px-4 py-8 hover:bg-brand-teal-300/10 transition-colors hover:text-brand-teal-500"
          >
            <IconPlusFilled class="w-6 h-6" />
            <span>Create Deck</span>
          </RouterLink>
        </template>

        <template v-slot="{ item: deck }">
          <DeckListItem :deck="deck" class="sm:min-h-80" />
        </template>
      </CardGrid>

      <CardGrid
        title="Shared Decks"
        :items="sharedDecks"
        key="id"
        fallback="No shared decks"
      >
        <template v-slot="{ item: deck }">
          <DeckListItem :deck="deck" class="sm:min-h-80" />
        </template>
      </CardGrid>
    </main>
  </AuthenticatedLayout>
</template>
<script setup lang="ts">
import { RouterLink } from "vue-router";
import { AuthenticatedLayout } from "@/layouts/AuthenticatedLayout";
import { useAllDecksQuery } from "@/queries/decks";
import { Button } from "@/components/ui/button";
import DeckListItem from "./DeckListItem.vue";
import { computed } from "vue";
import * as T from "@/types";
import PageHeader from "@/components/PageHeader.vue";
import { IconPlusFilled } from "@/components/icons";
import CardGrid from "@/components/CardGrid.vue";

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
