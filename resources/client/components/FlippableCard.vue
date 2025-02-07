<template>
  <div
    class="flippable-card relative min-h-[20rem] perspective w-full flex items-center gap-4 text-brand-maroon-950"
    data-cy="flippable-card"
  >
    <div
      :class="{ 'rotate-y-180': currentCardSide === 'back' }"
      class="relative w-full h-full transition-transform duration-500 transform-style-preserve-3d"
    >
      <CardSideView
        v-for="{ side, label } in labelledCardSides"
        :key="label"
        :label="label === 'back' ? 'Back' : 'Front'"
        :showLabel="showLabels"
        :side="side"
        class="absolute w-full h-full backface-hidden color-maroon-950"
        :class="{
          'z-20': label === currentCardSide,
          'z-10': label !== currentCardSide,
          'bg-brand-oatmeal-50': label === 'front',
          'rotate-y-180 bg-brand-gold-300': label === 'back',
        }"
      >
        <template #prepend>
          <slot name="prepend" />
        </template>
        <template #append>
          <slot name="append" />
          <Button
            variant="ghost"
            class="bg-brand-maroon-800/5 hover:bg-brand-maroon-800/10 uppercase text-xs tracking-wider text-brand-maroon-950 font-sans"
            @click="flipCard"
          >
            Flip
          </Button>
        </template>
      </CardSideView>
    </div>
  </div>
</template>

<script setup lang="ts">
import { CardSideView } from "@/components/CardSideView";
import { ref, watch, computed } from "vue";
import { CardSide, CardSideName } from "@/types";
import { Button } from "@/components/ui/button";

const props = withDefaults(
  defineProps<{
    front: CardSide;
    back: CardSide;
    initialSideName?: CardSideName;
    showLabels?: boolean;
  }>(),
  {
    initialSideName: "front",
    showLabels: false,
  },
);

const currentCardSide = ref<CardSideName>(props.initialSideName);

watch(
  () => props.initialSideName,
  (newInitialSideName) => {
    currentCardSide.value = newInitialSideName;
  },
);

interface CardSideWithLabel {
  label: CardSideName;
  side: CardSide;
}

const labelledCardSides = computed((): CardSideWithLabel[] => {
  return [
    {
      label: "front",
      side: props.front,
    },
    {
      label: "back",
      side: props.back,
    },
  ];
});

// if the sides change, reset the initial side
watch(labelledCardSides, () => {
  currentCardSide.value = props.initialSideName;
});

function flipCard() {
  currentCardSide.value = currentCardSide.value === "front" ? "back" : "front";
}
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
</style>
