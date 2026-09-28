<template>
  <div
    class="relative rounded-sm has-[>button:focus-visible]:outline has-[>button:focus-visible]:outline-2 has-[>button:focus-visible]:outline-offset-4 has-[>button:focus-visible]:outline-brand-maroon-800 has-[>button:focus-visible]:z-10"
    @click="emit('select')"
  >
    <button
      type="button"
      class="sr-only"
      :aria-pressed="side.status === 'selected'"
      :aria-disabled="isMatched"
      :aria-labelledby="`${contentId} ${positionId}`"
      @click.stop="emit('select')"
    />
    <MatchingSide
      :id="contentId"
      :blocks="side.blocks"
      :label="side.label"
      :status="side.status"
    />
    <span :id="positionId" hidden>tile {{ position }}</span>
  </div>
</template>
<script setup lang="ts">
import { computed, useId } from "vue";
import MatchingSide from "./MatchingSide.vue";
import { MatchingCardSide } from "./matchingGameStore";

const props = defineProps<{
  side: MatchingCardSide;
  position: number;
}>();

const emit = defineEmits<{
  (eventName: "select"): void;
}>();

const contentId = useId();
const positionId = useId();

const isMatched = computed(
  () => props.side.status === "match" || props.side.status === "disabled",
);
</script>
