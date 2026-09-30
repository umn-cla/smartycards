<template>
  <div class="flex items-center justify-center">
    <template v-if="modelValue">
      <IconCheckCircle class="text-brand-teal-300" />
      <span class="sr-only">Yes</span>
    </template>
    <template v-else>
      <span aria-hidden="true">-</span>
      <span class="sr-only">No</span>
    </template>
  </div>
</template>
<script setup lang="ts">
import IconCheckCircle from "@/components/icons/IconCheckCircle.vue";

defineProps<{
  modelValue: boolean;
}>();
</script>
<style scoped></style>
