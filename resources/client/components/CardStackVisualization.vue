<template>
  <div class="hidden sm:flex items-center gap-3 justify-center my-4">
    <!-- Counter -->
    <div class="text-sm font-medium text-brand-maroon-900/70 min-w-[5rem] text-right">
      {{ totalCards }} left
    </div>

    <!-- Stack Container -->
    <div class="relative h-12 flex items-center" :style="{ width: stackWidth }">
      <!-- Card Rectangles -->
      <div
        v-for="(cardData, index) in visibleCards"
        :key="cardData.key"
        class="stack-card absolute rounded border"
        :class="{
          'bg-brand-oatmeal-50 border-brand-maroon-800 shadow-sm': index === 0 && animationState !== 'reinserting',
          'bg-brand-oatmeal-50/70 border-brand-maroon-800/30': index > 0 || animationState === 'reinserting',
          'animate-puff-out': animationState === 'removing' && index === 0,
          'animate-shuffle-card': animationState === 'reinserting',
        }"
        :style="getCardStyle(index)"
      />

      <!-- Overflow indicator -->
      <div
        v-if="totalCards > MAX_VISIBLE_CARDS"
        class="absolute text-lg text-brand-maroon-900/30"
        :style="{ left: overflowIndicatorLeft }"
      >
        +{{ totalCards - MAX_VISIBLE_CARDS }}
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from "vue";

const props = defineProps<{
  totalCards: number;
  animationState: "idle" | "removing" | "reinserting";
  reinsertionIndex: number | null;
}>();

const CARD_WIDTH = 40;
const CARD_HEIGHT = 28;
const CARD_OFFSET_X = 8;
const MAX_VISIBLE_CARDS = 50;

const visibleCards = computed(() => {
  const count = Math.min(props.totalCards, MAX_VISIBLE_CARDS);
  return Array.from({ length: count }, (_, i) => ({
    key: `card-${i}`,
    index: i,
  }));
});

const stackWidth = computed(() => {
  const cardCount = Math.min(props.totalCards, MAX_VISIBLE_CARDS);
  return `${cardCount * CARD_OFFSET_X + CARD_WIDTH + 40}px`;
});

const overflowIndicatorLeft = computed(() => {
  const cardCount = Math.min(props.totalCards, MAX_VISIBLE_CARDS);
  return `${cardCount * CARD_OFFSET_X + CARD_WIDTH + 8}px`;
});

function getCardStyle(index: number) {
  // Create varied shuffle offsets for each card
  const shuffleOffset = (index % 3) === 0 ? -12 : (index % 3) === 1 ? 8 : -4;
  const shuffleRotate = (index % 4) === 0 ? -3 : (index % 4) === 1 ? 3 : (index % 4) === 2 ? -2 : 2;

  return {
    width: `${CARD_WIDTH}px`,
    height: `${CARD_HEIGHT}px`,
    left: `${index * CARD_OFFSET_X}px`,
    zIndex: MAX_VISIBLE_CARDS - index,
    '--shuffle-offset': `${shuffleOffset}px`,
    '--shuffle-rotate': `${shuffleRotate}deg`,
  };
}
</script>

<style scoped>
.stack-card {
  transition: all 0.3s ease;
  will-change: transform, opacity;
}
</style>
