<template>
  <li class="flex gap-1 items-stretch">
    <RouterLink
      :to="`/decks/${deck.id}`"
      class="flex-1"
    >
      <div class="bg-black/5 p-4 rounded-lg flex gap-4 items-start h-full">
        <div class="flex-1">
          <h3 class="font-bold text-xl text-neutral-900">
            {{ deck.name }}
          </h3>
          <h4
            class="font-bold text-xl text-black/30"
            v-if="deck.description"
          >
            {{ deck.description }}
          </h4>

          <p class="text-sm">
            {{ deck.cards_count }} cards
            <span v-if="deck.memberships_count && deck.memberships_count > 1">
              • You + {{ deck.memberships_count - 1 }} members
            </span>
          </p>
        </div>

        <MoreDeckActions :deck="deck" />
      </div>
    </RouterLink>

    <!-- stats -->
    <div class="flex flex-col gap-2 border rounded-lg py-2 px-4">
      <Tuple label="Last Practiced">
        {{ lastAttemptedAtTimeAgo ?? '-' }}
      </Tuple>
      <Tuple label="Avg Score">
        {{ deck.avg_score?.toFixed(2) ?? '-' }}
      </Tuple>
    </div>
  </li>
</template>

<script setup lang="ts">
import * as T from '@/types';
import Tuple from '@/components/Tuple.vue';
import { useTimeAgo } from '@vueuse/core';
import MoreDeckActions from './MoreDeckActions.vue';

const props = defineProps<{
  deck: T.Deck;
}>();

const lastAttemptedAtTimeAgo = useTimeAgo(() => props.deck.last_attempted_at);
</script>
