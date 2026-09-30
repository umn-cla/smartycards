import { toValue, watch, type MaybeRefOrGetter, type Ref } from "vue";
import { useRoute } from "vue-router";
import { useAnnouncer } from "@vue-a11y/announcer";
import { escapeHtmlText } from "./escapeHtmlText";

const APP_TITLE = "SmartyCards";

let isTitleAnnouncementPending = false;

function toDocumentTitle(parts: (string | undefined)[]): string {
  const namedParts = parts.filter((part): part is string => Boolean(part));
  return [...namedParts, APP_TITLE].join(" - ");
}

/**
 * Resets the title until the next page sets its own, and
 * has that page announce its title. Call once per in-app
 * navigation, before the next page renders.
 */
export function resetPageTitleForNavigation(): void {
  document.title = APP_TITLE;
  isTitleAnnouncementPending = true;
}

/**
 * True once the query's current request has succeeded or
 * failed once. Retries after a failure do not count.
 */
export function hasQueryResponded(query: {
  isFetching: Readonly<Ref<boolean>>;
  failureCount: Readonly<Ref<number>>;
}): boolean {
  return !query.isFetching.value || query.failureCount.value > 0;
}

/**
 * Sets the page's title as "Part - Part - SmartyCards".
 * WCAG 2.4.2 asks a single-page app to retitle each view.
 * An in-app navigation has no page load, which is when
 * screen readers announce a new page, so the title is
 * also announced once after each navigation. Until
 * `enabled` is true, the title stays "SmartyCards" and
 * nothing is announced; pass the page's data readiness,
 * like TanStack Query's `enabled`.
 */
export function usePageTitle(
  parts: MaybeRefOrGetter<(string | undefined)[]>,
  { enabled = true }: { enabled?: MaybeRefOrGetter<boolean> } = {},
): void {
  const route = useRoute();
  const { polite } = useAnnouncer();

  watch(
    [
      () => route.fullPath,
      () => toDocumentTitle(toValue(parts)),
      () => toValue(enabled),
    ],
    ([, title, isEnabled]) => {
      if (!isEnabled) return;
      document.title = title;
      if (!isTitleAnnouncementPending) return;
      isTitleAnnouncementPending = false;
      // VueAnnouncer sets messages as innerHTML. Without
      // escapeHtmlText, a deck name in the title becomes
      // markup and routeChange.cy.ts fails.
      polite(escapeHtmlText(title));
    },
    // Run after render, when a reused deck page's query has
    // moved to the new deck, not while it has the old one.
    { immediate: true, flush: "post" },
  );
}
