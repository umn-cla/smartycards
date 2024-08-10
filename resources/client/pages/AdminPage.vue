<template>
  <div></div>
</template>
<script setup lang="ts">
import config from '@/config';
import { onBeforeMount } from 'vue';

onBeforeMount(() => {
  // redirect to admin page on api server
  window.location.href = config.api.adminUrl;
});
</script>
<style scoped></style>
