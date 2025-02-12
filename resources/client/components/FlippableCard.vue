<template>
  <div class="flippable-card max-w-screen-sm text-brand-maroon-900 persective">
    <div
      v-for="side in ['front', 'back']"
      :key="side"
      :label="side"
      :class="[
        `card-side card-side__${side} p-2 flex flex-col gap-1 min-h-[20rem] transition-transform duration-500 transform-style-preserve-3d backface-hidden rounded-lg`,
        {
          'bg-brand-oatmeal-50': side === 'front',
          'bg-brand-gold-300': side === 'back',
          'card-side--is-active z-20': side === currentCardSide,
          'card-side--is-inactive rotate-y-180 z-10': side !== currentCardSide,
        },
      ]"
    >
      <h2
        v-if="showLabels"
        class="card-side__label text-xs uppercase text-center text-black/30"
      >
        {{ side }}
      </h2>
      <slot name="prepend" />
      <slot :name="side" />

      <slot name="append" />

      <Button
        variant="ghost"
        class="flip-button bg-brand-maroon-800/5 hover:bg-brand-maroon-800/10 uppercase text-xs tracking-wider text-brand-maroon-950 font-sans"
        @click="flipCard"
      >
        Flip
      </Button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch } from "vue";
import { CardSideName } from "@/types";
import { Button } from "@/components/ui/button";

const props = withDefaults(
  defineProps<{
    initialSideName?: CardSideName;
    showLabels?: boolean;
  }>(),
  {
    initialSideName: "front",
    showLabels: false,
  },
);

const currentCardSide = ref<CardSideName>(props.initialSideName);

function flipCard() {
  currentCardSide.value = currentCardSide.value === "front" ? "back" : "front";
}

// if initialSideName prop changes, update currentCardSide
watch(
  () => props.initialSideName,
  (newInitialSideName) => {
    currentCardSide.value = newInitialSideName;
  },
);
</script>

<style scoped>
.perspective {
  perspective: 1000px;
}
.transform-style-preserve-3d {
  transform-style: preserve-3d;
}
.backface-hidden {
  backface-visibility: hidden;
}
.rotate-y-180 {
  transform: rotateY(180deg);
}

.flippable-card {
  /* use grid so we can avoid absolute positioning
  * then we don't need to worry about collapsing parent height
  */
  display: grid;
}

.card-side {
  grid-row: 1/2;
  grid-column: 1/2;
}
</style>
