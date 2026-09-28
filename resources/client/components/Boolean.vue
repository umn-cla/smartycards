<template>
  <div class="flex items-center justify-center">
    <template v-if="modelValue">
      <IconCheckCircle class="text-brand-teal-300" />
      <span class="sr-only">Yes</span>
    </template>
    <span v-else> - </span>
  </div>
</template>
<script setup lang="ts">
import IconCheckCircle from "@/components/icons/IconCheckCircle.vue";

defineProps<{
  modelValue: boolean;
}>();
</script>
<style scoped></style>
