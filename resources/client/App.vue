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
import { useAnnouncer } from "@vue-a11y/announcer";
import ErrorModal from "./components/ErrorModal.vue";
import { escapeHtml } from "./lib/escapeHtml";
// import { VueQueryDevtools } from '@tanstack/vue-query-devtools';

const TITLE_ANNOUNCEMENT_DELAY_MS = 500;
const { polite } = useAnnouncer();

useRouter().afterEach((_to, from, failure) => {
  if (failure || from === START_LOCATION) return;

  nextTick(() => {
    document.getElementById("main-content")?.focus({ preventScroll: true });
  });

  setTimeout(() => {
    // Without escapeHtml, VueAnnouncer's innerHTML parses
    // a deck name as markup and routeChange.cy.ts fails
    polite(escapeHtml(document.title));
  }, TITLE_ANNOUNCEMENT_DELAY_MS);
});
</script>
<style scoped></style>
