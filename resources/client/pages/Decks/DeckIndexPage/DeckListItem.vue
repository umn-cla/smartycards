<template>
  <RouterLink
    :to="`/decks/${deck.id}`"
    class="h-full w-full p-2 pt-1 rounded-xl border-[0.66rem] border-brand-gold-500 bg-brand-gold-500/50 flex flex-col gap-4"
    :class="{
      'shadow-solid-gold-1px translate-y-[-1px]':
        1 <= cardCount && cardCount < 3,
      'shadow-solid-gold-1 -translate-y-1': 3 <= cardCount && cardCount < 6,
      'shadow-solid-gold-2 -translate-y-2': 6 <= cardCount && cardCount < 9,
      'shadow-solid-gold-3 -translate-y-3': 9 <= cardCount,
    }"
  >
    <aside class="flex justify-end translate-x-2">
      <MoreDeckActions :deck="deck" />
    </aside>
    <div
      class="items-center justify-center flex flex-col flex-1 font-serif text-center"
    >
      <h3 class="font-bold text-2xl text-neutral-900">
        {{ deck.name }}
      </h3>
      <h4 class="font-bold text-lg text-neutral-900/30" v-if="deck.description">
        {{ deck.description }}
      </h4>
    </div>

    <footer
      class="flex gap-4 justify-end items-center flex-wrap text-xs text-neutral-900/50 pt-2"
    >
      <p class="text-">
        {{ deck.cards_count }} cards
        <span v-if="deck.memberships_count && deck.memberships_count > 1">
          • You + {{ deck.memberships_count - 1 }} members
        </span>
      </p>
    </footer>
  </RouterLink>
</template>

<script setup lang="ts">
import * as T from "@/types";
import Tuple from "@/components/Tuple.vue";
import { useTimeAgo } from "@vueuse/core";
import MoreDeckActions from "./MoreDeckActions.vue";
import { computed } from "vue";
import { Badge } from "@/components/ui/badge";

const props = defineProps<{
  deck: T.Deck;
}>();

const lastAttemptedAtTimeAgo = useTimeAgo(() => props.deck.last_attempted_at);
const cardCount = computed(() => props.deck.cards_count ?? 0);
</script>
