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
    <main v-if="deck">
      <div class="flex items-center justify-center">
        <RouterLink
          :to="{ name: 'decks.practice', params: { deckId } }"
          class="bg-brand-teal-500 text-white px-8 py-4 rounded-lg text-4xl shadow-solid-teal-2"
        >
          Let's Practice
        </RouterLink>
      </div>

      <section class="my-8">
        <header
          class="flex justify-between items-baseline sticky top-16 lg:top-0 z-10 bg-brand-oatmeal-100 py-4"
        >
          <h3 class="text-3xl font-bold">Cards</h3>
          <Button @click="flipAllCards" variant="secondary"> Flip All </Button>
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
              :front="card.front"
              :back="card.back"
              :initialSideName="initialCardSide"
            >
              <template #prepend>
                <div class="flex justify-between items-center">
                  <ScoreDots
                    :score="card.avg_score / 3"
                    title="Difficulty"
                    class="my-1"
                    v-if="card.attempts_count"
                  />
                  <span
                    v-else
                    class="text-xs font-sans bg-umn-gold-300 rounded-full px-2 leading-none flex items-center py-1"
                    >New</span
                  >
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
import { computed } from "vue";
import { Button } from "@/components/ui/button";
import MoreDeckActions from "@/pages/Decks/DeckIndexPage/MoreDeckActions.vue";
import PageHeader from "@/components/PageHeader.vue";
import FlippableCard from "@/components/FlippableCard.vue";
import IconPlusFilled from "@/components/icons/IconPlusFilled.vue";
import ScoreDots from "@/components/ScoreDots.vue";
import MoreCardActions from "./MoreCardActions.vue";
import { ref } from "vue";

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

function handleDeleteCard(card: T.Card) {
  deleteCard(card);
}

const initialCardSide = ref<T.CardSideName>("front");

function flipAllCards() {
  initialCardSide.value = initialCardSide.value === "front" ? "back" : "front";
}
</script>
<style scoped></style>
