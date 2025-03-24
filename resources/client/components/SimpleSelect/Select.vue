<template>
  <select
    class="w-28 bg-brand-maroon-800/5 border-none rounded-md text-sm"
    :value="modelValue ?? ''"
    @input="
      $emit('update:modelValue', ($event.target as HTMLSelectElement).value)
    "
  >
    <!-- see note about iOS: https://vuejs.org/guide/essentials/forms.html#select -->
    <SelectOption value="" disabled>{{ placeholder }}</SelectOption>
    <slot />
  </select>
</template>
<script setup lang="ts">
import SelectOption from "./SelectOption.vue";

withDefaults(
  defineProps<{
    modelValue: string | null;
    placeholder?: string;
  }>(),
  {
    placeholder: "Select",
  },
);

defineEmits<{
  (event: "update:modelValue", value: string): void;
}>();
</script>
<style scoped>
select {
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='rgb(160, 139, 139)' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
}
</style>
