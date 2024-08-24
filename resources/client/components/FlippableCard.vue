<template>
  <div class="flex items-center gap-4 text-brand-maroon-950">
    <div class="relative min-w-56 w-full h-80 perspective">
      <div
        :class="{ 'rotate-y-180': isShowingBack }"
        class="relative w-full h-full transition-transform duration-500 transform-style-preserve-3d"
      >
        <CardSideView
          v-for="(side, index) in [front, back]"
          :key="index"
          :label="index ? 'Back' : 'Front'"
          :side="side"
          class="absolute w-full h-full backface-hidden color-maroon-950"
          :class="{
            'bg-brand-oatmeal-50': index === 0,
            'rotate-y-180 bg-brand-gold-500': index === 1,
          }"
        >
          <template #prepend>
            <slot name="prepend" />
          </template>
          <template #append>
            <slot name="append" />
            <Button
              variant="ghost"
              class="bg-black/5 hover:bg-black/10 uppercase text-xs tracking-wider text-brand-maroon-950 font-sans"
              @click="isShowingBack = !isShowingBack"
            >
              Flip
            </Button>
          </template>
        </CardSideView>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { CardSideView } from "@/components/CardSideView";
import { ref, watch } from "vue";
import { CardSide } from "@/types";
import { Button } from "@/components/ui/button";

const props = defineProps<{
  front: CardSide;
  back: CardSide;
  initialSide?: "front" | "back";
}>();

const isShowingBack = ref(props.initialSide === "back" ?? false);

// if the sides change, reset the initial side
watch([() => props.front, () => props.back], () => {
  isShowingBack.value = props.initialSide === "back";
});
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
