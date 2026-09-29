import { computed, type ComputedRef } from "vue";
import { useRoute } from "vue-router";
import { useDeckByIdQuery } from "@/queries/decks";

export interface DocumentTitleState {
  title: string;
  isWaitingForDeck: boolean;
}

function toDocumentTitle(parts: (string | undefined)[]): string {
  const namedParts = parts.filter((part): part is string => Boolean(part));
  return [...namedParts, "SmartyCards"].join(" - ");
}

/**
 * Title state for the current route. WCAG 2.4.2 asks a
 * single-page app to retitle each view. An in-app
 * navigation has no page load, which is when screen
 * readers announce a new page, so the caller writes
 * `title` to document.title and also announces it.
 * Announce only once `isWaitingForDeck` is false, or
 * routes with `hasDeckNameInTitle` are announced
 * without the deck name.
 */
export function useDocumentTitleState(): ComputedRef<DocumentTitleState> {
  const route = useRoute();
  const titleDeckId = computed(() =>
    route.meta.hasDeckNameInTitle ? Number(route.params.deckId) : null,
  );
  const {
    data: titleDeck,
    isFetching: isFetchingTitleDeck,
    failureCount: titleDeckFailureCount,
  } = useDeckByIdQuery(titleDeckId);

  return computed(() => {
    // Waiting on isFetching alone also waits out TanStack's
    // 3 retries (about 7 s), and the failed-request test in
    // routeChange.cy.ts times out.
    const isFirstDeckRequestPending =
      isFetchingTitleDeck.value && titleDeckFailureCount.value === 0;
    return {
      title: toDocumentTitle([route.meta.title, titleDeck.value?.name]),
      isWaitingForDeck: titleDeckId.value !== null && isFirstDeckRequestPending,
    };
  });
}
