<template>
  <div v-if="blocks.length === 0" class="text-brand-maroon-900/50 italic">
    (empty)
  </div>
  <CardSideView
    v-else
    :side="blocks"
    :side-name="sideName"
    :show-label="false"
    class="text-sm overflow-auto bg-white mx-auto w-48 h-64"
  />
</template>

<script setup lang="ts">
import type { CardSide, CardSideName } from "@/types";
import CardSideView from "@/components/CardSideView/CardSideView.vue";

withDefaults(
  defineProps<{
    blocks: CardSide;
    sideName?: CardSideName;
  }>(),
  {
    sideName: "front",
  },
);
</script>
