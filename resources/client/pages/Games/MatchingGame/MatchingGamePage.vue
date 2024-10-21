<template>
  <AuthenticatedLayout>
    <header
      class="flex mb-6 border border-black/10 p-2 sm:p-4 rounded-xl mx-auto max-w-screen-sm gap-2 items-center"
      v-if="deck"
    >
      <Button asChild variant="secondary" class="!px-2">
        <RouterLink
          :to="{ name: 'decks.show', params: { deckId: deckId } }"
          class="flex items-center"
        >
          <IconChevronLeft class="size-6" />
          <span class="sr-only">Back</span>
        </RouterLink>
      </Button>
      <div class="flex gap-2 sm:gap-4 items-baseline">
        <h1 class="font-bold text-brand-maroon-800 text-lg sm:text-xl">
          {{ deck?.name }}
        </h1>
        <h2
          class="hidden sm:block text-brand-maroon-800/50 text-sm sm:text-base"
          v-if="deck.description"
        >
          {{ deck?.description }}
        </h2>
      </div>
    </header>

    <div v-if="deck" class="max-w-screen-sm mx-auto">
      <div
        v-if="deck.cards.length < 2"
        class="bg-brand-oatmeal-50 p-4 rounded-xl text-center max-w-screen-sm mx-auto"
      >
        <p class="mb-4">You need at least 2 cards to play</p>
        <Button asChild>
          <RouterLink :to="{ name: 'decks.show', params: { deckId: deckId } }">
            Add Cards
          </RouterLink>
        </Button>
      </div>

      <MatchingGame
        :cards="deck.cards"
        class="mx-auto max-h-[66vh] aspect-square"
      />
    </div>
  </AuthenticatedLayout>
</template>
<script setup lang="ts">
import AuthenticatedLayout from "@/layouts/AuthenticatedLayout/AuthenticatedLayout.vue";
import { Button } from "@/components/ui/button";
import { computed, ref } from "vue";
import { useDeckByIdQuery } from "@/queries/decks";
import IconChevronLeft from "@/components/icons/IconChevronLeft.vue";
import MatchingGame from "./MatchingGame.vue";

const props = defineProps<{
  deckId: number;
}>();

const deckIdRef = computed(() => props.deckId);

const { data: deck, isLoading: isDeckLoading } = useDeckByIdQuery(deckIdRef);
</script>
<style scoped></style>
