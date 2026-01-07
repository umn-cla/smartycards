<template>
  <EmbedLayout>
    <Alert
      v-if="isLtiLaunch && hasCompletedPractice"
      type="success"
      message="Practice Complete"
      class="max-w-screen-sm mx-auto mb-6"
    />
    <ActivityPageHeader :title="deck ? deck.name : 'Practice Deck'">
      <template #actions>
        <StartingSideSelect v-model="initialSideName" />
      </template>
    </ActivityPageHeader>

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
        @init="handleResetPractice"
        @complete="handlePracticeComplete"
      />
    </div>
    <LevelProgress
      :xp="deckStats?.current_user_xp ?? 0"
      class="w-full px-4 py-2 fixed bottom-0 left-0 right-0 max-w-screen-sm mx-auto"
    />
  </EmbedLayout>
</template>
<script setup lang="ts">
import { computed } from "vue";
import EmbedLayout from "@/layouts/EmbedLayout.vue";
import { Button } from "@/components/ui/button";
import LevelProgress from "@/components/LevelProgress.vue";
import PracticeDeck from "./PracticeDeck.vue";
import ActivityPageHeader from "../ActivityPageHeader.vue";
import Alert from "@/components/Alert.vue";
import StartingSideSelect from "@/components/StartingSideSelect.vue";
import { usePracticeDeck } from "@/composables/usePracticeDeck";
import { useLtiContext } from "@/composables/useLtiContext";

const props = defineProps<{
  deckId: number;
}>();

const deckIdRef = computed(() => props.deckId);
const { launchId, isLtiLaunch } = useLtiContext();

const {
  initialSideName,
  deck,
  isDeckLoading,
  deckStats,
  hasCompletedPractice,
  handlePracticeComplete,
  handleResetPractice,
} = usePracticeDeck({ deckId: deckIdRef, isLtiContext: isLtiLaunch });
</script>
<style scoped>
button {
  &:hover {
    text-decoration: none;
  }
}
</style>
