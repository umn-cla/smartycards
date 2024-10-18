<template>
  <div :style="containerStyles">
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
import { ref, watch, computed } from "vue";
import { CardSide, CardSideName } from "@/types";
import { Button } from "@/components/ui/button";
import { convertToPx } from "./lib/utils";
import { transformOrigin } from "radix-vue/dist/Popper";

// width and height on normal cards.
// See FlippableCard.vue for the default values
// maybe this should be a constant somewhere
const ORIGINAL_SIDE_WIDTH = "16rem";
const ORIGINAL_SIDE_HEIGHT = "24rem";
const DEFAULT_THUMB_SIDE_WIDTH = "8rem";

const props = withDefaults(
  defineProps<{
    side: CardSide;
    label?: string;
    width?: string;
    aspectRactio?: "default" | "square";
  }>(),
  {
    width: DEFAULT_THUMB_SIDE_WIDTH,
    aspectRatio: "default",
  },
);

const scaleFactor = computed(() => {
  const widthPx = convertToPx(props.width);
  const defaultWidthPx = convertToPx(ORIGINAL_SIDE_WIDTH);
  return widthPx / defaultWidthPx;
});

// the side view should have the original width
// and thumb height, and use transform scale to
// scale it down
const sideViewStyles = computed(() => ({
  width: ORIGINAL_SIDE_WIDTH,
  height:
    props.aspectRactio === "square"
      ? ORIGINAL_SIDE_WIDTH
      : ORIGINAL_SIDE_HEIGHT,
  transform: `scale(${scaleFactor.value})`,
  transformOrigin: "top left",
}));

// to "shrink-wrap" the side view, we set the
// the container styles to the scaled down size
// Is there a better way to do this?
const containerStyles = computed(() => ({
  width: props.width,
  height:
    props.aspectRactio === "square"
      ? props.width
      : `calc(${ORIGINAL_SIDE_HEIGHT} * ${scaleFactor.value})`,
}));
</script>

<style scoped></style>
