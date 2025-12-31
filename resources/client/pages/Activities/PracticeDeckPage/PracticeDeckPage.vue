<template>
  <AuthenticatedLayout>
    <header
      class="flex mb-4 sm:mb-6 -mt-2 sm:mt-0 flex-col mx-auto max-w-screen-sm gap-4"
      v-if="deck"
    >
      <h1
        class="font-bold text-brand-maroon-800 text-lg sm:text-xl text-center leading-none hidden sm:block"
      >
        {{ deck.name }}
      </h1>
      <div class="flex items-center justify-between w-full flex-wrap">
        <StartingSideSelect v-model="initialSideName" />
        <Button asChild variant="secondary">
          <RouterLink
            :to="{ name: 'decks.show', params: { deckId: props.deckId } }"
            class="flex gap-2 items-center"
          >
            End Practice
          </RouterLink>
        </Button>
      </div>
    </header>

    <div>
      <div v-if="isDeckLoading" class="text-center">...</div>
      <div v-else-if="deck && deck.cards.length < 2" class="text-center">
        <p>You need at least 2 cards to practice.</p>
        <Button class="mt-4" asChild>
          <RouterLink :to="`/decks/${deckId}/cards/create`">
            Add a Card
          </RouterLink>
        </Button>
      </div>
      <PracticeDeck
        v-else-if="deck"
        :deck="deck"
        :initialSideName="initialSideName"
        @complete="handlePracticeComplete"
      />
    </div>
    <LevelProgress
      :xp="deckStats?.current_user_xp ?? 0"
      class="w-full px-4 py-1 fixed bottom-0 left-0 right-0"
    />
  </AuthenticatedLayout>
</template>
<script setup lang="ts">
import { computed } from "vue";
import { AuthenticatedLayout } from "@/layouts/AuthenticatedLayout";
import { Button } from "@/components/ui/button";
import LevelProgress from "@/components/LevelProgress.vue";
import PracticeDeck from "./PracticeDeck.vue";
import StartingSideSelect from "@/components/StartingSideSelect.vue";
import { usePracticeDeck } from "@/composables/usePracticeDeck";

const props = defineProps<{
  deckId: number;
}>();

const deckIdRef = computed(() => props.deckId);

const { initialSideName, deck, isDeckLoading, deckStats, handlePracticeComplete } =
  usePracticeDeck({ deckId: deckIdRef });
</script>
<style scoped>
button {
  &:hover {
    text-decoration: none;
  }
}
</style>
