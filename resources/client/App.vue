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
import { escapeHtmlText } from "./lib/escapeHtmlText";
// import { VueQueryDevtools } from '@tanstack/vue-query-devtools';

const TITLE_ANNOUNCEMENT_DELAY_MS = 500;
const { polite } = useAnnouncer();
let pendingTitleAnnouncement: ReturnType<typeof setTimeout> | undefined;

useRouter().afterEach((_to, from, failure) => {
  if (failure || from === START_LOCATION) return;

  clearTimeout(pendingTitleAnnouncement);

  nextTick(() => {
    document.getElementById("main-content")?.focus({ preventScroll: true });
  });

  pendingTitleAnnouncement = setTimeout(() => {
    // VueAnnouncer sets messages as innerHTML. Without
    // escapeHtmlText, a deck name in the title becomes
    // markup and routeChange.cy.ts fails.
    polite(escapeHtmlText(document.title));
  }, TITLE_ANNOUNCEMENT_DELAY_MS);
});
</script>
<style scoped></style>
