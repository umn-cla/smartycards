<template>
  <SimpleTooltip :label="label">
    <button
      @click="$emit('update:modelValue', !modelValue)"
      class="text-sm flex items-center gap-1 p-2 rounded-md"
      :class="{
        'text-brand-maroon-800 bg-brand-oatmeal-50 rounded-md': modelValue,
        'text-brand-maroon-600': !modelValue,
      }"
      :aria-label="label"
    >
      <slot />
    </button>
  </SimpleTooltip>
</template>
<script setup lang="ts">
import SimpleTooltip from "@/components/SimpleTooltip.vue";

defineProps<{
  modelValue: boolean;
  label: string;
}>();

defineEmits<{
  (name: "update:modelValue", value: boolean): void;
}>();
</script>
<style scoped></style>
