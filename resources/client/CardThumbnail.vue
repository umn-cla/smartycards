<template>
  <div ref="containerRef" :style="containerStyles">
    <CardSideView
      :label="label"
      :showLabel="false"
      :side="side"
      class="bg-brand-oatmeal-50"
      :style="sideViewStyles"
    >
      <template #prepend>
        <slot name="prepend" />
      </template>
      <template #append>
        <slot name="append" />
      </template>
    </CardSideView>
  </div>
</template>

<script setup lang="ts">
import { CardSideView } from "@/components/CardSideView";
import { ref, computed } from "vue";
import { CardSide, CardSideName } from "@/types";
import { convertToPx } from "./lib/utils";

// width and height on normal cards.
// See FlippableCard.vue for the default values
// maybe this should be a constant somewhere
const ORIGINAL_SIDE_WIDTH = "8rem";
const ORIGINAL_SIDE_HEIGHT = "24rem";

const props = withDefaults(
  defineProps<{
    side: CardSide;
    label?: string;
    aspectRatio?: "default" | "square";
  }>(),
  {
    aspectRatio: "default",
  },
);

const containerRef = ref<HTMLElement | null>(null);

const containerWidth = computed(() => {
  return containerRef.value?.clientWidth ?? 0;
});

const scaleFactor = computed(() => {
  const defaultWidthPx = convertToPx(ORIGINAL_SIDE_WIDTH);
  return containerWidth.value / defaultWidthPx || 1;
});

// the side view should have the original width
// and thumb height, and use transform scale to
// scale it down
const sideViewStyles = computed(() => ({
  transform: `scale(${scaleFactor.value})`,
  transformOrigin: "top left",
  width: ORIGINAL_SIDE_WIDTH,
  height:
    props.aspectRatio === "square" ? ORIGINAL_SIDE_WIDTH : ORIGINAL_SIDE_HEIGHT,
}));

// to "shrink-wrap" the side view, we set the
// the container styles to the scaled down size
// Is there a better way to do this?
const containerStyles = computed(() => ({
  height:
    props.aspectRatio === "square"
      ? `calc(${ORIGINAL_SIDE_WIDTH} * ${scaleFactor.value})`
      : `calc(${ORIGINAL_SIDE_HEIGHT} * ${scaleFactor.value})`,
}));
</script>

<style scoped></style>
