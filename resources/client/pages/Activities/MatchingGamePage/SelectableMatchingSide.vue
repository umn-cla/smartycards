<template>
  <!-- Keep @click here: mouse clicks land on MatchingSide,
    and another @click on the button selects twice. -->
  <div
    class="selectable-matching-side relative rounded-sm"
    @click="emit('select')"
  >
    <button
      type="button"
      class="sr-only"
      :aria-pressed="side.status === 'selected'"
      :aria-disabled="isMatched"
      :aria-labelledby="`${contentId} ${positionId}`"
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
import { isMatchedSide, MatchingCardSide } from "./matchingGameStore";

const props = defineProps<{
  side: MatchingCardSide;
  position: number;
}>();

const emit = defineEmits<{
  (eventName: "select"): void;
}>();

const contentId = useId();
const positionId = useId();

const isMatched = computed(() => isMatchedSide(props.side));
</script>
<style scoped>
.selectable-matching-side:has(> button:focus-visible) {
  @apply z-10 outline outline-2 outline-offset-4 outline-brand-maroon-800;
}
</style>
