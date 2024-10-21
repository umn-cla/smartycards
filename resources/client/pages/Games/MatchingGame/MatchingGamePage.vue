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
      <section v-else-if="['in-progress', 'setup'].includes(state.gameState)">
        <h2 class="text-center font-bold text-xl mb-4">Matching</h2>
        <MatchingGame
          :cards="gameCards"
          @gameover="
            () => {
              state.wins++;
              state.gameState = 'complete';
            }
          "
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

      <section v-else-if="state.gameState === 'complete'">
        <h2 class="text-center font-bold text-xl mb-4">Matching</h2>

        <div
          class="flex flex-col gap-4 items-center bg-brand-teal-300/25 rounded-md text-brand-maroon-950 p-4 roud"
        >
          <h3 class="text-4xl">You Win!</h3>
          <p>You have {{ state.wins }} {{ pluralize(state.wins, "win") }}.</p>
          <Button @click="startGame">Play Again</Button>
        </div>
      </section>

      <section v-else>
        <h2 class="text-center font-bold text-xl mb-4">Matching</h2>

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
import { pluralize } from "@/utils/pluralize";

const props = defineProps<{
  deckId: number;
}>();

// 8 cards gives 4 pairs so we get a 4x4 grid
const MAX_CARDS_FOR_GAME = 8;

const state = reactive({
  gameState: "setup" as "setup" | "in-progress" | "complete" | "error", // | "loading" // | "setup"
  cardSide: "front" as T.CardSideName,
  numberOfCards: 8,
  wins: 0,
});

const deckIdRef = computed(() => props.deckId);

const { data: deck, isLoading: isDeckLoading } = useDeckByIdQuery(deckIdRef);

const gameCards = computed((): T.Card[] => {
  if (!deck.value) {
    return [];
  }

  const numberOfCards = Math.min(deck.value.cards.length, MAX_CARDS_FOR_GAME);

  return toShuffled(deck.value.cards).slice(0, numberOfCards);
});

async function startGame() {
  state.gameState = "in-progress";
}
</script>
<style scoped></style>
