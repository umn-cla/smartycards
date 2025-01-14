<template>
  <button
    @click="$emit('update:modelValue', !modelValue)"
    :class="
      cn(
        'text-sm flex items-center gap-1 p-2 rounded-md',
        {
          'bg-brand-maroon-800 text-brand-oatmeal-50': modelValue,
          'text-brand-maroon-800/30': !modelValue,
        },
        props.class,
      )
    "
    :aria-pressed="modelValue"
    :aria-label="label"
    :title="label"
  >
    <slot />
  </button>
</template>
<script setup lang="ts">
import type { HTMLAttributes } from "vue";
import { cn } from "@/lib/utils";

const props = defineProps<{
  modelValue: boolean;
  label: string;
  class?: HTMLAttributes["class"];
}>();

defineEmits<{
  (name: "update:modelValue", value: boolean): void;
}>();
</script>
<style scoped></style>
