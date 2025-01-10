<template>
  <select
    class="w-28 bg-brand-maroon-800/5 border-none rounded-md text-sm"
    :modelValue="modelValue"
    @change="
      $emit('update:modelValue', ($event.target as HTMLSelectElement).value)
    "
  >
    <slot />
  </select>
</template>
<script setup lang="ts">
defineProps<{
  modelValue: string;
}>();

defineEmits<{
  (event: "update:modelValue", value: string): void;
}>();
</script>
<style scoped>
select {
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='rgb(160, 139, 139)' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
}
</style>
