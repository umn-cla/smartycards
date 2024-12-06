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
          <Select v-model="state.initialSideName" id="starting-side-select">
            <SelectTrigger class="w-28 bg-brand-maroon-800/5">
              <SelectValue placeholder="Starting side" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="front">Front</SelectItem>
              <SelectItem value="back">Back</SelectItem>
              <SelectItem value="random">Random</SelectItem>
            </SelectContent>
          </Select>
        </div>
        <div class="hidden sm:flex items-center justify-center">
          <Button
            @click="state.orientation = 'portrait'"
            :variant="
              state.orientation === 'portrait' ? 'default' : 'secondary'
            "
            class="uppercase text-xs tracking-wider rounded-r-none"
          >
            Tall
          </Button>
          <Button
            @click="state.orientation = 'landscape'"
            :variant="
              state.orientation === 'landscape' ? 'default' : 'secondary'
            "
            class="uppercase text-xs tracking-wider rounded-l-none"
          >
            Wide
          </Button>
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
        :orientation="state.orientation"
        @complete="handlePracticeComplete"
      />
    </div>
  </AuthenticatedLayout>
</template>
<script setup lang="ts">
import { computed, reactive, provide } from "vue";
import { AuthenticatedLayout } from "@/layouts/AuthenticatedLayout";
import { useDeckByIdQuery } from "@/queries/decks";
import * as T from "@/types";
import { Button } from "@/components/ui/button";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import { Label } from "@/components/ui/label";
import { useCreateDeckActivityEventMutation } from "@/queries/deckActivityEvents/useCreateDeckActivityEventMutation";
import LevelProgress from "@/components/LevelProgress.vue";
import { useDeckStatsQuery } from "@/queries/decks/useDeckStatsQuery";
import PracticeDeck from "./PracticeDeck.vue";
import { IS_DECK_TTS_ENABLED_INJECTION_KEY } from "@/constants";
import { useIsDeckTTSEnabled } from "@/composables/useIsDeckTTSEnabled";

const props = defineProps<{
  deckId: number;
}>();

const state = reactive({
  initialSideName: "front" as T.CardSideName | "random",
  orientation: "portrait" as "portrait" | "landscape",
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
