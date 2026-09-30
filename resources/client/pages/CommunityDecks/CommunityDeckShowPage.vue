<template>
  <AuthenticatedLayout>
    <PageHeader
      v-if="deck"
      class="mb-8"
      :title="`Preview ${deck.name}`"
      :subtitle="deck.description"
      :backLabel="`Community Decks`"
      :backTo="{ name: 'community.decks.index' }"
    >
    </PageHeader>
    <div v-if="deck">
      <section class="my-8">
        <header class="my-4 flex justify-between items-baseline">
          <h2 ref="cardsHeading" tabindex="-1" class="text-3xl font-bold">
            Cards
          </h2>
          <div class="flex gap-1">
            <Button @click="flipAllCards" variant="secondary">
              Flip All
            </Button>
            <Button
              v-if="deck.capabilities.canJoinAsViewer"
              @click="handleJoinDeck"
              >Join</Button
            >
            <Button
              v-else-if="deck.capabilities.canLeave"
              @click="leaveDeckAndFocusCardsHeading"
              variant="destructive"
              >Leave Deck</Button
            >
          </div>
        </header>
        <div class="card-grid">
          <FlippableCard
            v-for="card in deck.cards"
            :key="card.id"
            :front="card.front"
            :back="card.back"
            :initialSideName="initialCardSide"
          />
        </div>
      </section>
    </div>
  </AuthenticatedLayout>
</template>
<script setup lang="ts">
import { AuthenticatedLayout } from "@/layouts/AuthenticatedLayout";
import { useDeckByIdQuery } from "@/queries/decks";
import * as T from "@/types";
import { computed } from "vue";
import { Button } from "@/components/ui/button";
import PageHeader from "@/components/PageHeader.vue";
import FlippableCard from "@/components/FlippableCard.vue";
import { ref } from "vue";
import { useJoinCommunityDeckMutation } from "@/queries/community";
import { useLeaveDeckMutation } from "@/queries/deckMemberships";
import { useRouter } from "vue-router";
import { useAnnouncer } from "@vue-a11y/announcer";
import { hasQueryResponded, usePageTitle } from "@/lib/usePageTitle";

const props = defineProps<{
  deckId: number;
}>();

const deckIdRef = computed(() => props.deckId);

const deckQuery = useDeckByIdQuery(deckIdRef);
const { data: deck } = deckQuery;
usePageTitle(() => ["Preview Deck", deck.value?.name], {
  enabled: () => hasQueryResponded(deckQuery),
});

const initialCardSide = ref<T.CardSideName>("front");
const announcer = useAnnouncer();

function flipAllCards() {
  initialCardSide.value = initialCardSide.value === "front" ? "back" : "front";
  announcer.polite(`All cards now show the ${initialCardSide.value}.`);
}

const { mutate: joinDeck } = useJoinCommunityDeckMutation();

const router = useRouter();
async function handleJoinDeck() {
  await joinDeck(deckIdRef.value);
  router.push({ name: "decks.show", params: { deckId: deckIdRef.value } });
}
const { mutate: leaveDeck } = useLeaveDeckMutation();

const cardsHeading = ref<HTMLHeadingElement | null>(null);

function leaveDeckAndFocusCardsHeading(): void {
  cardsHeading.value?.focus();
  leaveDeck(deckIdRef.value);
}
</script>
<style scoped></style>
