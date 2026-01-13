<template>
  <AuthenticatedLayout>
    <main>
      <PageHeader title="Decks" size="lg" class="mb-8" />

      <div class="flex justify-between items-center mb-4">
        <h3 class="text-3xl font-bold text-brand-maroon-800">My Decks</h3>
        <div class="flex items-center gap-2">
          <label for="sort-select" class="text-sm text-brand-maroon-800/70">
            Sort by:
          </label>
          <Select v-model="sortBy" id="sort-select">
            <SelectTrigger class="w-40 bg-white">
              <SelectValue />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="updated">Last updated</SelectItem>
              <SelectItem value="name">Deck name</SelectItem>
              <SelectItem value="cards">Number of cards</SelectItem>
            </SelectContent>
          </Select>
        </div>
      </div>

      <section>
        <div class="card-grid !gap-6 sm:gap-4">
          <RouterLink
            :to="{ name: 'decks.create' }"
            class="bg-brand-maroon-800/2 flex w-full h-full items-center justify-center rounded-xl flex-col gap-2 border-2 border-dashed border-black/10 px-4 py-8 hover:bg-brand-teal-300/10 transition-colors hover:text-brand-teal-500"
          >
            <IconPlusFilled class="w-6 h-6" />
            <span>Create Deck</span>
          </RouterLink>

          <DeckListItem
            :deck="deck"
            v-for="deck in sortedMyDecks"
            :key="deck.id"
          />
        </div>
      </section>

      <section class="my-8">
        <h3 class="text-3xl font-bold text-brand-maroon-800 mb-4">
          Shared Decks
        </h3>
        <div class="card-grid !gap-6 sm:gap-4" v-if="sortedSharedDecks.length">
          <DeckListItem
            :deck="deck"
            v-for="deck in sortedSharedDecks"
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
import { computed, ref } from "vue";
import * as T from "@/types";
import PageHeader from "@/components/PageHeader.vue";
import { IconPlusFilled } from "@/components/icons";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";

const { data: decks } = useAllDecksQuery();

const sortBy = ref<"updated" | "name" | "cards">("updated");

const sortDecks = (deckList: T.Deck[]): T.Deck[] => {
  const sorted = [...deckList];

  switch (sortBy.value) {
    case "name":
      return sorted.sort((a, b) => a.name.localeCompare(b.name));
    case "cards":
      return sorted.sort((a, b) => (b.cards_count ?? 0) - (a.cards_count ?? 0));
    case "updated":
    default:
      return sorted.sort((a, b) => {
        const dateA = new Date(a.updated_at).getTime();
        const dateB = new Date(b.updated_at).getTime();
        return dateB - dateA;
      });
  }
};

const myDecks = computed((): T.Deck[] => {
  return (
    decks.value?.filter(
      (deck) => deck.current_user_role === T.MembershipRole.OWNER,
    ) ?? []
  );
});

const sharedDecks = computed((): T.Deck[] => {
  return (
    decks.value?.filter(
      (deck) => deck.current_user_role !== T.MembershipRole.OWNER,
    ) ?? []
  );
});

const sortedMyDecks = computed((): T.Deck[] => {
  return sortDecks(myDecks.value);
});

const sortedSharedDecks = computed((): T.Deck[] => {
  return sortDecks(sharedDecks.value);
});
</script>
<style scoped></style>
