<template>
  <div>
    <b class="text-xs text-black/50 font-normal">{{ label }}</b>
    <div class="text-sm text-right">
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
