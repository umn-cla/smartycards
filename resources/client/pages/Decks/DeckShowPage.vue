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
        <Button
          asChild
          variant="secondary"
        >
          <RouterLink
            :to="{ name: 'decks.share', params: { deckId } }"
            class="btn"
            v-if="deck.capabilities.canViewMemberships"
          >
            Share
          </RouterLink>
        </Button>
        <Button asChild>
          <RouterLink
            :to="{ name: 'decks.practice', params: { deckId } }"
            class="btn"
          >
            Practice
          </RouterLink>
        </Button>
      </div>
    </PageHeader>
    <main v-if="deck">
      <header class="flex justify-between mb-6">
        <h3 class="font-bold text-4xl">Cards</h3>
        <Button
          asChild
          variant="secondary"
        >
          <RouterLink
            v-if="canEdit"
            :to="{ name: 'cards.create', params: { deckId } }"
          >
            Add Card
          </RouterLink>
        </Button>
      </header>

      <ul
        v-if="deck.cards?.length"
        class="flex gap-4 flex-col"
      >
        <li
          v-for="card in deck.cards"
          :key="card.id"
          class="flex gap-2"
        >
          <template
            v-for="sideName in ['front', 'back']"
            :key="sideName"
          >
            <div class="flex-1 flex flex-col">
              <ColHeader class="capitalize">{{ sideName }}</ColHeader>
              <RouterLink
                :to="{ name: 'cards.edit', params: { deckId, cardId: card.id } }"
                class="contents"
              >
                <CardSideView
                  :side="card[sideName as T.CardSideName]"
                  :label="sideName"
                  class="flex-1"
                />
              </RouterLink>
            </div>
          </template>
          <div class="flex flex-col">
            <ColHeader>Stats</ColHeader>
            <CardStats
              :card="card"
              class="flex-1"
            />
          </div>
          <div
            class="flex flex-col"
            v-if="deck.capabilities.canUpdate || deck.capabilities.canDelete"
          >
            <ColHeader>Actions</ColHeader>
            <CardActions
              :card="card"
              v-if="canEdit || canDelete"
              class="flex-1"
              @delete="() => handleDeleteCard(card)"
            />
          </div>
        </li>
      </ul>
      <p v-else>No cards yet</p>
    </main>
  </AuthenticatedLayout>
</template>
<script setup lang="ts">
import { AuthenticatedLayout } from '@/layouts/AuthenticatedLayout';
import { useDeleteCardMutation } from '@/queries/cards';
import { useDeckByIdQuery } from '@/queries/decks';
import * as T from '@/types';
import { RouterLink } from 'vue-router';
import { computed } from 'vue';
import CardSideView from '@/components/CardSideView.vue';
import CardStats from '@/components/CardStats.vue';
import CardActions from '@/components/CardActions.vue';
import ColHeader from '@/components/ColHeader.vue';
import { Button } from '@/components/ui/button';
import MoreDeckActions from './DeckIndexPage/MoreDeckActions.vue';
import PageHeader from '@/components/PageHeader.vue';

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
</script>
<style scoped></style>
