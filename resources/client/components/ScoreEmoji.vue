<template>
  <div class="text-sm">
    <span v-if="score > 0.6">✅</span>
    <span v-else-if="score > 0.3">🫤</span>
    <span v-else>❌</span>
  </div>
</template>

<script setup lang="ts">
defineProps<{
  score: number; // 0-1
}>();
</script>

<style scoped></style>
