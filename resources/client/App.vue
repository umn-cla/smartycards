<template>
  <div class="min-h-dvh flex flex-col font-sans text-brand-maroon-800">
    <ErrorModal />
    <RouterView />
    <VueAnnouncer class="sr-only" />
    <!-- <VueQueryDevtools /> -->
  </div>
</template>
<script setup lang="ts">
import { RouterView } from "vue-router";
import ErrorModal from "./components/ErrorModal.vue";
// import { VueQueryDevtools } from '@tanstack/vue-query-devtools';
</script>
<style scoped></style>
