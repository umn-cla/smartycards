import { toValue, watch, type MaybeRefOrGetter } from "vue";
import { useAnnouncer } from "@vue-a11y/announcer";
import { escapeHtmlText } from "./escapeHtmlText";

let isTitleAnnouncementPending = false;

function toDocumentTitle(parts: (string | undefined)[]): string {
  const namedParts = parts.filter((part): part is string => Boolean(part));
  return [...namedParts, "SmartyCards"].join(" - ");
}

/** Call from router.afterEach for each in-app navigation. */
export function announceNextPageTitle(): void {
  isTitleAnnouncementPending = true;
}

/**
 * Sets the page's title as "Part - Part - SmartyCards" and
 * announces it to screen readers after an in-app
 * navigation. Nothing happens until `enabled` is true.
 *
 * Cases, each with a spec in routeChange.cy.ts:
 * 1. Each page has its own title (WCAG 2.4.2).
 *    "titles a deck page and the page after it"
 * 2. An in-app navigation has no page load for screen
 *    readers to announce, so the title is announced once.
 *    "focuses the main content and announces the page
 *    after keyboard navigation"
 * 3. The first page load is a real page load, which
 *    screen readers announce, so it is not announced.
 *    "does not announce the first page load"
 * 4. A deck page passes `enabled` until its deck query
 *    settles, so the title includes the deck name.
 *    "announces a deck page once its deck has loaded"
 *    "announces a deck page whose deck request fails"
 * 5. Moving between decks reuses the page component, and
 *    the new deck is announced, not the old one.
 *    "announces the new deck when moving from one deck
 *    page to another"
 * 6. A deck name containing HTML is announced as text.
 *    "announces a deck name containing HTML as text"
 */
export function usePageTitle(
  parts: MaybeRefOrGetter<(string | undefined)[]>,
  { enabled = true }: { enabled?: MaybeRefOrGetter<boolean> } = {},
): void {
  const { polite } = useAnnouncer();

  watch(
    [() => toDocumentTitle(toValue(parts)), () => toValue(enabled)],
    ([title, isEnabled]) => {
      // Case 4
      if (!isEnabled) return;

      // Case 1
      document.title = title;

      // Cases 2 and 3
      if (!isTitleAnnouncementPending) return;
      isTitleAnnouncementPending = false;

      // Case 6: VueAnnouncer sets messages as innerHTML.
      polite(escapeHtmlText(title));
    },
    // Case 5: run after render, when a reused deck page's
    // query has moved to the new deck, not while it has
    // the old one.
    { immediate: true, flush: "post" },
  );
}
