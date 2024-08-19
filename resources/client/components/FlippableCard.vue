<template>
  <div class="flex items-center gap-4 text-brand-maroon-950">
    <div
      class="relative w-[340px] aspect-[2/3] perspective"
      @click="isShowingBack = !isShowingBack"
    >
      <div
        :class="{ 'rotate-y-180': isShowingBack }"
        class="relative w-full h-full transition-transform duration-500 transform-style-preserve-3d"
      >
        <CardSideView
          label="Front"
          :side="front"
          class="absolute w-full h-full backface-hidden bg-brand-oatmeal-100 color-maroon-950"
        >
          <Button
            variant="ghost"
            class="bg-black/5 hover:bg-black/10 uppercase text-xs tracking-wider text-brand-maroon-950 font-sans"
          >
            Flip
          </Button>
        </CardSideView>
        <CardSideView
          label="Back"
          :side="back"
          class="absolute w-full h-full backface-hidden bg-brand-gold-500 color-maroon-950 rotate-y-180"
        >
          <Button
            variant="ghost"
            class="bg-black/5 hover:bg-black/10 uppercase text-xs tracking-wider text-brand-maroon-950 font-sans"
          >
            Flip
          </Button>
        </CardSideView>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { CardSideView } from "@/components/CardSideView";
import { ref } from "vue";
import { CardSide } from "@/types";
import { Button } from "@/components/ui/button";

const props = defineProps<{
  front: CardSide;
  back: CardSide;
  initialSide?: "front" | "back";
}>();

const isShowingBack = ref(props.initialSide === "back" ?? false);
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
