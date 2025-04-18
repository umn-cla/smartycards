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
        <div class="flex gap-1 items-baseline">
          <Label for="starting-side-select" class="sr-only">Start Side</Label>
          <SimpleSelect
            v-model="state.initialSideName"
            id="starting-side-select"
          >
            <SelectOption value="front">Front</SelectOption>
            <SelectOption value="back">Back</SelectOption>
            <SelectOption value="random">Random</SelectOption>
          </SimpleSelect>
        </div>
        <Button asChild variant="secondary">
          <RouterLink
            :to="{ name: 'decks.show', params: { deckId: props.deckId } }"
            class="flex gap-2 items-center"
          >
            End Practice
          </RouterLink>
        </Button>
      </div>
      <LevelProgress
        :xp="deckStats?.current_user_xp ?? 0"
        class="w-full px-2"
      />
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
        :initialSideName="state.initialSideName"
        @complete="handlePracticeComplete"
      />
    </div>
  </AuthenticatedLayout>
</template>
<script setup lang="ts">
import { computed, reactive } from "vue";
import { AuthenticatedLayout } from "@/layouts/AuthenticatedLayout";
import { useDeckByIdQuery } from "@/queries/decks";
import * as T from "@/types";
import { Button } from "@/components/ui/button";
import { SimpleSelect, SelectOption } from "@/components/SimpleSelect";
import { Label } from "@/components/ui/label";
import { useCreateDeckActivityEventMutation } from "@/queries/deckActivityEvents/useCreateDeckActivityEventMutation";
import LevelProgress from "@/components/LevelProgress.vue";
import { useDeckStatsQuery } from "@/queries/decks/useDeckStatsQuery";
import PracticeDeck from "./PracticeDeck.vue";

const props = defineProps<{
  deckId: number;
}>();

const state = reactive({
  initialSideName: "front" as T.CardSideName | "random",
});

const deckIdRef = computed(() => props.deckId);

const { data: deck, isLoading: isDeckLoading } = useDeckByIdQuery(deckIdRef);

const { data: deckStats } = useDeckStatsQuery(deckIdRef);

const { mutate: createActivityEvent } = useCreateDeckActivityEventMutation();

async function handlePracticeComplete(cardCount: number) {
  await createActivityEvent({
    deckId: deck.value?.id ?? 0,
    activityType: T.ActivityTypeName.PRACTICE_ALL_CARDS,
    correctCount: cardCount,
    totalCount: cardCount,
  });
}
</script>
<style scoped>
button {
  &:hover {
    text-decoration: none;
  }
}
</style>
