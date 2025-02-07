<template>
  <EmbedLayout>
    <AcitivityPageHeader title="Matching" :subtitle="deck?.name ?? ''">
      <LevelProgress
        :xp="deckStats?.current_user_xp ?? 0"
        class="w-full px-2"
      />
    </AcitivityPageHeader>

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
        @gameover="handleWin"
      />
    </div>
  </EmbedLayout>
</template>
<script setup lang="ts">
import EmbedLayout from "@/layouts/EmbedLayout.vue";
import { Button } from "@/components/ui/button";
import { computed } from "vue";
import { useDeckByIdQuery } from "@/queries/decks";
import MatchingGame from "./MatchingGame.vue";
import { useCreateDeckActivityEventMutation } from "@/queries/deckActivityEvents/useCreateDeckActivityEventMutation";
import LevelProgress from "@/components/LevelProgress.vue";
import { useDeckStatsQuery } from "@/queries/decks/useDeckStatsQuery";
import * as T from "@/types";
import AcitivityPageHeader from "../AcitivityPageHeader.vue";

const props = defineProps<{
  deckId: number;
}>();

const deckIdRef = computed(() => props.deckId);

const { data: deck, isLoading: isDeckLoading } = useDeckByIdQuery(deckIdRef);
const { data: deckStats } = useDeckStatsQuery(deckIdRef);

const { mutate: createActivityEvent } = useCreateDeckActivityEventMutation();

async function handleWin(matchedPairs: number) {
  if (!deck.value) {
    throw new Error("Cannot handle win condition without a deck");
  }

  await createActivityEvent({
    deckId: deck.value.id,
    activityType: T.ActivityTypeName.MATCHING,
    correctCount: matchedPairs,
    totalCount: matchedPairs,
  });
}
</script>
<style scoped></style>
