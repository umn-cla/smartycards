<template>
  <div>
    <b
      class="text-xs font-normal uppercase block text-center"
      :class="labelClass"
      >{{ label }}</b
    >
    <div class="text-sm text-right">
      <slot />
    </div>
  </div>
</template>
<script setup lang="ts">
import { CSSClass } from "@/types";

withDefaults(
  defineProps<{
    label: string;
    labelClass?: CSSClass;
  }>(),
  {
    labelClass: "text-brand-maroon-800/50",
  },
);
</script>
<style scoped></style>
