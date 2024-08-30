<template>
  <div class="">
    <audio :src="src" controls class="max-w-full"></audio>
  </div>
</template>
<script setup lang="ts">
defineProps<{
  src: string;
}>();
</script>
<style scoped></style>
