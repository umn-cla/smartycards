<template>
  <div class="flex items-center justify-center">
    <IconCheckCircle v-if="modelValue" class="text-brand-teal-300" />
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
