<template>
  <div class="min-h-dvh flex flex-col font-sans text-brand-maroon-800">
    <ErrorModal />
    <RouterView />
    <VueAnnouncer class="sr-only" />
    <!-- <VueQueryDevtools /> -->
  </div>
</template>
<script setup lang="ts">
import { nextTick } from "vue";
import { RouterView, START_LOCATION, useRouter } from "vue-router";
import ErrorModal from "./components/ErrorModal.vue";
import { announceNextPageTitle } from "./lib/usePageTitle";
// import { VueQueryDevtools } from '@tanstack/vue-query-devtools';

useRouter().afterEach((_to, from, failure) => {
  if (failure || from === START_LOCATION) return;

  announceNextPageTitle();

  nextTick(() => {
    document.getElementById("main-content")?.focus({ preventScroll: true });
  });
});
</script>
<style scoped></style>
