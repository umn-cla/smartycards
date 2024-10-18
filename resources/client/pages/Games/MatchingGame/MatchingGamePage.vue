<template>
  <AuthenticatedLayout>
    <header
      class="flex mb-6 border border-black/10 p-2 sm:p-4 rounded-xl mx-auto max-w-screen-sm gap-2 items-center"
      v-if="deck"
    >
      <Button asChild variant="secondary" class="!px-2">
        <RouterLink
          :to="{ name: 'decks.show', params: { deckId: props.deckId } }"
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
      <div v-if="deck.cards.length < CARDS_FOR_GAME">
        <p>You need at least {{ CARDS_FOR_GAME }} cards to play</p>
        <Button asChild>
          <router-link :to="{ name: 'deck', params: { id: deck.id } }">
            Add Cards
          </router-link>
        </Button>
      </div>
      <!-- <section class="flex flex-col gap-4" v-if="state.gameState === 'setup'">
        <h2 class="text-center font-bold text-xl">Set Up</h2>

        <div class="flex justify-center">
          <Button @click="startGame">Let's Play</Button>
        </div>
      </section> -->

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
import { computed, reactive } from "vue";
import { useDeckByIdQuery } from "@/queries/decks";
import * as T from "@/types";
import IconChevronLeft from "@/components/icons/IconChevronLeft.vue";
import MatchingGame from "./MatchingGame.vue";
import { toShuffled } from "@/lib/utils";

const props = defineProps<{
  deckId: number;
}>();

// 8 cards gives 4 pairs so we get a 4x4 grid
const CARDS_FOR_GAME = 8;

const state = reactive({
  gameState: "in-progress" as "in-progress" | "complete" | "error", // | "loading" // | "setup"
  cardSide: "front" as T.CardSideName,
});

const deckIdRef = computed(() => props.deckId);

const { data: deck, isLoading: isDeckLoading } = useDeckByIdQuery(deckIdRef);

const gameCards = computed((): T.Card[] => {
  if (!deck.value) {
    return [];
  }

  const numOfPickedCards = Math.min(deck.value.cards.length, CARDS_FOR_GAME);

  return toShuffled(deck.value.cards).slice(0, numOfPickedCards);
});

async function startGame() {
  state.gameState = "in-progress";
}
</script>
<style scoped></style>
