<template>
  <div
    :to="`/decks/${deck.id}`"
    class="h-full w-full p-2 pt-1 rounded-xl bg-brand-gold-300 flex flex-col gap-4 border border-brand-gold-shadow sm:h-[24rem]"
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
      class="items-center justify-center flex flex-1 flex-col font-serif text-center"
    >
      <h3 class="font-bold text-xl text-brand-maroon-800">
        {{ deck.name }}
      </h3>
      <h4 class="font-bold text-brand-maroon-800/50" v-if="deck.description">
        {{ deck.description }}
      </h4>
      <div
        class="font-sans text-[0.66rem] text-brand-maroon-900/60 text-center uppercase flex flex-col items-center justify-center mt-4 gap-1"
      >
        <b>Level {{ currentLevel }}</b>
        <Progress :modelValue="percentToNextLevel" class="w-16 !h-1" />
        <p>{{ xpEarnedForCurrentLevel }} / {{ xpNeeded }} XP</p>
      </div>
    </div>

    <footer
      class="flex gap-4 justify-center items-center flex-wrap text-xs text-brand-maroon-800/50 pt-2"
    >
      <p>
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
import MoreDeckActions from "./MoreDeckActions.vue";
import { computed } from "vue";
import { IconArrowRight } from "@/components/icons";
import { Button } from "@/components/ui/button";
import { Progress } from "@/components/ui/progress";
import {
  getLevelFromTotalXP,
  getXPEarnedAtThisLevel,
  getXPNeededAtThisLevel,
} from "@/lib/getXPLevel";

const props = defineProps<{
  deck: T.Deck;
}>();

const cardCount = computed(() => props.deck.cards_count ?? 0);

// const randInt = (min: number, max: number) =>
//   Math.floor(Math.random() * (max - min + 1) + min);

// const randomPercent = randInt(1, 100);
// const randomLevel = randInt(1, 10);

const totalXP = computed(() => props.deck.current_user_details.xp);

const currentLevel = computed(() => getLevelFromTotalXP(totalXP.value));

const xpNeeded = computed(() => getXPNeededAtThisLevel(currentLevel.value));

const xpEarnedForCurrentLevel = computed(() =>
  getXPEarnedAtThisLevel(totalXP.value),
);

const percentToNextLevel = computed(() => {
  return (xpEarnedForCurrentLevel.value / xpNeeded.value) * 100;
});
</script>
