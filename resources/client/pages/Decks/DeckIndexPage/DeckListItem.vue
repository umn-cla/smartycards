<template>
  <div
    :to="`/decks/${deck.id}`"
    class="h-full w-full p-2 pt-1 rounded-xl bg-brand-gold-500/50 flex flex-col gap-4 border border-brand-gold-shadow sm:h-[24rem]"
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
      <h3 class="font-bold text-xl text-neutral-900">
        {{ deck.name }}
      </h3>
      <h4 class="font-bold text-neutral-900/50" v-if="deck.description">
        {{ deck.description }}
      </h4>
    </div>

    <footer
      class="flex gap-4 justify-center items-center flex-wrap text-xs text-neutral-900/50 pt-2"
    >
      <p class="text-">
        {{ deck.cards_count }} cards
        <span v-if="deck.memberships_count && deck.memberships_count > 1">
          • {{ deck.memberships_count }} members
        </span>
      </p>
    </footer>
    <Button asChild variant="secondary">
      <RouterLink :to="`/decks/${deck.id}`">
        <IconArrowRight class="w-5 h-5" />
      </RouterLink>
    </Button>
  </div>
</template>

<script setup lang="ts">
import * as T from "@/types";
import { useTimeAgo } from "@vueuse/core";
import MoreDeckActions from "./MoreDeckActions.vue";
import { computed } from "vue";
import { IconArrowRight } from "@/components/icons";
import { Button } from "@/components/ui/button";

const props = defineProps<{
  deck: T.Deck;
}>();

const lastAttemptedAtTimeAgo = useTimeAgo(() => props.deck.last_attempted_at);
const cardCount = computed(() => props.deck.cards_count ?? 0);
</script>
