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
    <main v-if="deck">
      <section class="my-8">
        <header class="my-4 flex justify-between items-baseline">
          <h3 class="text-3xl font-bold">Cards</h3>
          <div class="flex gap-1">
            <Button @click="flipAllCards" variant="secondary">
              Flip All
            </Button>
            <Button
              v-if="deck.capabilities.canJoinAsViewer"
              @click="joinDeck(deck.id)"
              >Join</Button
            >
            <Button
              v-else-if="deck.capabilities.canLeave"
              @click="leaveDeck(deck.id)"
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
    </main>
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

const props = defineProps<{
  deckId: number;
}>();

const deckIdRef = computed(() => props.deckId);

const { data: deck } = useDeckByIdQuery(deckIdRef);

const initialCardSide = ref<T.CardSideName>("front");

function flipAllCards() {
  initialCardSide.value = initialCardSide.value === "front" ? "back" : "front";
}

const { mutate: joinDeck } = useJoinCommunityDeckMutation();
const { mutate: leaveDeck } = useLeaveDeckMutation();
</script>
<style scoped></style>
