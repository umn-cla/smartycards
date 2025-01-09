<template>
  <AuthenticatedLayout>
    <PageHeader
      v-if="deck"
      :title="deck.name"
      :subtitle="deck.description"
      :backLabel="`Decks`"
      :backTo="{ name: 'decks.index' }"
    >
      <div class="flex gap-2">
        <MoreDeckActions :deck="deck" />
        <Button asChild variant="outline">
          <RouterLink
            v-if="deck.capabilities.canViewReports"
            :to="{ name: 'decks.reports.summary', params: { deckId } }"
          >
            Report
          </RouterLink>
        </Button>
        <Button asChild>
          <RouterLink
            :to="{ name: 'decks.share', params: { deckId } }"
            v-if="deck.capabilities.canViewMemberships"
          >
            Share
          </RouterLink>
        </Button>
      </div>
    </PageHeader>
    <LevelProgress
      :xp="deck?.current_user_details.xp ?? 0"
      class="mt-8 mb-12"
    />
    <main v-if="deck">
      <div class="grid grid-cols-3 gap-4">
        <RouterLink
          :to="{ name: 'decks.practice', params: { deckId } }"
          class="bg-brand-teal-500 text-white px-4 py-2 text-center font-bold sm:px-8 sm:py-4 rounded-lg sm:text-4xl shadow-solid-teal-2"
        >
          Practice
          <p class="text-xs sm:text-base font-normal text-white/75">
            +{{ practiceXP }} XP
          </p>
        </RouterLink>
        <RouterLink
          :to="{ name: 'decks.quiz', params: { deckId } }"
          class="bg-brand-blue-500 px-4 py-2 sm:px-8 sm:py-4 text-center font-bold rounded-lg sm:text-4xl shadow-solid-blue-2 text-white"
        >
          Quiz
          <p class="text-xs sm:text-base font-normal text-white/75">
            +{{ quizXP }} XP
          </p>
        </RouterLink>
        <RouterLink
          :to="{ name: 'decks.games.matching', params: { deckId } }"
          class="bg-purple-700 px-4 py-2 sm:px-8 sm:py-4 text-center font-bold rounded-lg sm:text-4xl shadow-solid-purple-900 text-white"
        >
          Matching
          <p class="text-xs sm:text-base font-normal text-white/75">
            +{{ matchingXP }} XP
          </p>
        </RouterLink>
      </div>

      <section class="my-8">
        <header
          class="flex justify-between items-baseline sticky top-16 lg:top-0 z-10 bg-brand-oatmeal-100 py-4"
        >
          <h3 class="text-3xl font-bold">Cards</h3>
          <div class="flex gap-4">
            <form class="relative">
              <IconSearch class="absolute left-2 top-2 w-4 h-4 z-10" />
              <Input
                placeholder="Search cards"
                class="pl-8 placeholder:text-black/40 h-full"
              />
            </form>
            <Button @click="flipAllCards" variant="secondary">
              Flip All
            </Button>
          </div>
        </header>
        <div class="card-grid">
          <RouterLink
            v-if="deck.capabilities.canUpdate"
            :to="{ name: 'cards.create' }"
            class="flex w-full h-full items-center justify-center rounded-xl flex-col gap-2 border-2 border-dashed border-black/10 px-4 py-8 hover:bg-brand-teal-300/10 transition-colors hover:text-brand-teal-500"
          >
            <IconPlusFilled class="w-6 h-6" />
            <span>Create Card</span>
          </RouterLink>

          <template v-for="card in deck.cards" :key="card.id">
            <FlippableCard
              data-cy="flippable-card"
              :front="card.front"
              :back="card.back"
              :initialSideName="initialCardSide"
            >
              <template #prepend>
                <div class="flex justify-between items-center">
                  <ScoreEmoji
                    :score="card.avg_score ? card.avg_score / 3 : null"
                    :attempts="card.attempts_count"
                    title="Difficulty"
                    class="my-1"
                  />
                  <MoreCardActions
                    :canDelete="canDelete"
                    :canEdit="canEdit"
                    :card="card"
                    @delete="handleDeleteCard"
                  />
                </div>
              </template>
            </FlippableCard>
          </template>
        </div>
      </section>
    </main>
  </AuthenticatedLayout>
</template>
<script setup lang="ts">
import { AuthenticatedLayout } from "@/layouts/AuthenticatedLayout";
import { useDeleteCardMutation } from "@/queries/cards";
import { useDeckByIdQuery } from "@/queries/decks";
import * as T from "@/types";
import { RouterLink } from "vue-router";
import { computed, provide } from "vue";
import { Button } from "@/components/ui/button";
import MoreDeckActions from "@/pages/Decks/DeckIndexPage/MoreDeckActions.vue";
import PageHeader from "@/components/PageHeader.vue";
import FlippableCard from "@/components/FlippableCard.vue";
import IconPlusFilled from "@/components/icons/IconPlusFilled.vue";
import ScoreEmoji from "@/components/ScoreEmoji.vue";
import MoreCardActions from "./MoreCardActions.vue";
import { ref } from "vue";
import LevelProgress from "@/components/LevelProgress.vue";
import { useActivityTypesQuery } from "@/queries/activityTypes/useActivityTypesQuery";
import { useIsDeckTTSEnabled } from "@/composables/useIsDeckTTSEnabled";
import { IS_DECK_TTS_ENABLED_INJECTION_KEY } from "@/constants";
import { Input } from "@/components/ui/input";
import { IconSearch } from "@/components/icons";

const props = defineProps<{
  deckId: number;
}>();

const deckIdRef = computed(() => props.deckId);

const canEdit = computed(() => {
  return deck.value?.capabilities.canUpdate ?? false;
});

const canDelete = computed(() => {
  return deck.value?.capabilities.canDelete ?? false;
});

const { data: deck } = useDeckByIdQuery(deckIdRef);
const { mutate: deleteCard } = useDeleteCardMutation();
const { data: activityTypes } = useActivityTypesQuery();

const xpByActivityTypeName = computed(() => {
  return activityTypes.value?.reduce(
    (acc, activityType) => ({
      ...acc,
      [activityType.name]: activityType.default_xp,
    }),
    {} as Record<T.ActivityTypeName, number>,
  );
});

const quizXP = computed(
  () => xpByActivityTypeName.value?.[T.ActivityTypeName.QUIZ] ?? 0,
);
const matchingXP = computed(
  () => xpByActivityTypeName.value?.[T.ActivityTypeName.MATCHING] ?? 0,
);
const practiceXP = computed(
  () =>
    xpByActivityTypeName.value?.[T.ActivityTypeName.PRACTICE_ALL_CARDS] ?? 0,
);

function handleDeleteCard(card: T.Card) {
  deleteCard(card);
}

const initialCardSide = ref<T.CardSideName>("front");

function flipAllCards() {
  initialCardSide.value = initialCardSide.value === "front" ? "back" : "front";
}

// provide info about TTS to any card blocks that need it
const { isDeckTTSEnabled } = useIsDeckTTSEnabled(deck);
provide(IS_DECK_TTS_ENABLED_INJECTION_KEY, isDeckTTSEnabled);
</script>
<style scoped></style>
