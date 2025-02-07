<template>
  <EmbedLayout>
    <AcitivityPageHeader title="Flashcards">
      <template #actions>
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
      </template>
      <LevelProgress
        :xp="deckStats?.current_user_xp ?? 0"
        class="w-full px-2"
      />
    </AcitivityPageHeader>

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
  </EmbedLayout>
</template>
<script setup lang="ts">
import { computed, reactive, provide, watch } from "vue";
import EmbedLayout from "@/layouts/EmbedLayout.vue";
import { useDeckByIdQuery } from "@/queries/decks";
import * as T from "@/types";
import { Button } from "@/components/ui/button";
import { SimpleSelect, SelectOption } from "@/components/SimpleSelect";
import { Label } from "@/components/ui/label";
import { useCreateDeckActivityEventMutation } from "@/queries/deckActivityEvents/useCreateDeckActivityEventMutation";
import LevelProgress from "@/components/LevelProgress.vue";
import { useDeckStatsQuery } from "@/queries/decks/useDeckStatsQuery";
import PracticeDeck from "./PracticeDeck.vue";
import { IS_DECK_TTS_ENABLED_INJECTION_KEY } from "@/constants";
import { useIsDeckTTSEnabled } from "@/composables/useIsDeckTTSEnabled";
import AcitivityPageHeader from "../AcitivityPageHeader.vue";

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

// provide info about TTS to any card blocks that need it
const { isDeckTTSEnabled } = useIsDeckTTSEnabled(deck);
provide(IS_DECK_TTS_ENABLED_INJECTION_KEY, isDeckTTSEnabled);
</script>
<style scoped>
button {
  &:hover {
    text-decoration: none;
  }
}
</style>
