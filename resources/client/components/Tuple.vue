<template>
  <div>
    <b class="text-xs text-black/50 font-normal">{{ label }}</b>
    <div class="text-sm">
      <slot />
    </div>
  </div>
</template>
<script setup lang="ts">
defineProps<{
  label: string;
}>();
</script>
<style scoped></style>
