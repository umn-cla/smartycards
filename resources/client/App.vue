<template>
  <div class="min-h-dvh flex flex-col font-sans text-brand-maroon-800">
    <ErrorModal />
    <RouterView />
    <VueAnnouncer class="sr-only" />
    <!-- <VueQueryDevtools /> -->
  </div>
</template>
<script setup lang="ts">
import { nextTick, ref, watch, watchPostEffect } from "vue";
import { RouterView, START_LOCATION, useRouter } from "vue-router";
import { useAnnouncer } from "@vue-a11y/announcer";
import ErrorModal from "./components/ErrorModal.vue";
import { escapeHtmlText } from "./lib/escapeHtmlText";
import { useDocumentTitleState } from "./lib/documentTitle";
// import { VueQueryDevtools } from '@tanstack/vue-query-devtools';

const { polite } = useAnnouncer();
const documentTitleState = useDocumentTitleState();
const isTitleAnnouncementPending = ref(false);

watchPostEffect(() => {
  document.title = documentTitleState.value.title;
});

useRouter().afterEach((_to, from, failure) => {
  if (failure || from === START_LOCATION) return;

  nextTick(() => {
    document.getElementById("main-content")?.focus({ preventScroll: true });
  });

  isTitleAnnouncementPending.value = true;
});

watch(
  [isTitleAnnouncementPending, documentTitleState],
  () => {
    const { title, isWaitingForDeck } = documentTitleState.value;
    if (!isTitleAnnouncementPending.value || isWaitingForDeck) return;
    isTitleAnnouncementPending.value = false;
    // VueAnnouncer sets messages as innerHTML. Without
    // escapeHtmlText, a deck name in the title becomes
    // markup and routeChange.cy.ts fails.
    polite(escapeHtmlText(title));
  },
  { flush: "post" },
);
</script>
<style scoped></style>
