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

    <div v-if="deck">
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

      <section
        class="flex flex-col gap-4"
        v-else-if="state.gameState === 'setup'"
      >
        <h2 class="text-center font-bold text-xl">Set Up</h2>
        <div class="flex flex-wrap gap-8 items-start mx-auto">
          <div v-if="deck.cards.length > 2">
            <Label>Number of Cards</Label>
            <div class="flex items-center gap-2">
              <input
                v-model="state.numberOfCards"
                type="range"
                :min="2"
                :max="maxCards"
                class="w-28 block h-8"
              />
              <span>{{ state.numberOfCards }}</span>
            </div>
          </div>
        </div>

        <div class="flex justify-center">
          <Button @click="state.gameState = 'in-progress'">Let's Play</Button>
        </div>
      </section>

      <section v-else-if="['in-progress', 'setup'].includes(state.gameState)">
        <h2 class="text-center font-bold text-xl mb-4">Matching</h2>

        <MatchingGame
          v-if="deck"
          :cards="gameCards"
          @gameover="state.gameState = 'complete'"
          class="mx-auto max-h-[66vh] aspect-square"
        />
      </section>

      <section v-else-if="state.gameState === 'error'">
        <h2 class="text-center font-bold text-xl text-brand-maroon-500">
          Error
        </h2>

        <div class="flex flex-col gap-4 items-center">
          <p>
            Something went wrong. If the problem persists, email
            <a href="latistecharch@umn.edu">latistecharch@umn.edu</a>
          </p>
          <Button @click="state.gameState = 'in-progress'">Try Again</Button>
        </div>
      </section>

      <section v-else>
        <h2 class="text-center font-bold text-xl">Complete</h2>

        <div class="flex flex-col gap-4 items-center">
          <Button @click="startGame">Try Again</Button>
        </div>
      </section>
    </div>
  </AuthenticatedLayout>
</template>
<script setup lang="ts">
import AuthenticatedLayout from "@/layouts/AuthenticatedLayout/AuthenticatedLayout.vue";
import { Button } from "@/components/ui/button";
import { computed, reactive, watch } from "vue";
import { useDeckByIdQuery } from "@/queries/decks";
import * as T from "@/types";
import IconChevronLeft from "@/components/icons/IconChevronLeft.vue";
import MatchingGame from "./MatchingGame.vue";
import { toShuffled } from "@/lib/utils";
import { Label } from "@/components/ui/label";

const props = defineProps<{
  deckId: number;
}>();

// 8 cards gives 4 pairs so we get a 4x4 grid
const MAX_CARDS_FOR_GAME = 8;

const state = reactive({
  gameState: "setup" as "setup" | "in-progress" | "complete" | "error", // | "loading" // | "setup"
  cardSide: "front" as T.CardSideName,
  numberOfCards: 8,
});

const maxCards = computed(() =>
  Math.min(deck.value?.cards.length ?? 0, MAX_CARDS_FOR_GAME),
);

const deckIdRef = computed(() => props.deckId);

const { data: deck, isLoading: isDeckLoading } = useDeckByIdQuery(deckIdRef);

watch(
  [deck],
  () => {
    // max sure the number of cards is within the range of the deck
    if (!deck || state.numberOfCards <= maxCards.value) {
      return;
    }
    state.numberOfCards = maxCards.value;
  },
  { immediate: true },
);

const gameCards = computed((): T.Card[] => {
  if (!deck.value) {
    return [];
  }

  return toShuffled(deck.value.cards).slice(0, state.numberOfCards);
});

async function startGame() {
  state.gameState = "in-progress";
}
</script>
<style scoped></style>
