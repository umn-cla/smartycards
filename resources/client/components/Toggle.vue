<template>
  <SimpleTooltip :label="label">
    <button
      @click="$emit('update:modelValue', !modelValue)"
      class="text-sm flex items-center gap-1 p-2 rounded-md"
      :class="{
        'bg-brand-maroon-800 text-brand-oatmeal-50': modelValue,
        'text-brand-maroon-800/50': !modelValue,
      }"
      :aria-label="label"
      v-bind="$attrs"
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
