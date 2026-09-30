import { toValue, watch, type MaybeRefOrGetter } from "vue";
import { useAnnouncer } from "@vue-a11y/announcer";
import { escapeHtmlText } from "./escapeHtmlText";

function toDocumentTitle(parts: (string | undefined)[]): string {
  const namedParts = parts.filter((part): part is string => Boolean(part));
  return [...namedParts, "SmartyCards"].join(" - ");
}

export function usePageTitle(
  parts: MaybeRefOrGetter<(string | undefined)[]>,
): void {
  const { polite } = useAnnouncer();

  watch(
    () => toDocumentTitle(toValue(parts)),
    (title) => {
      document.title = title;

      // we might still be loading data like deck name, so if
      // not all title parts are available yet, just hold off to
      // avoid announcing the title until all parts are available.
      if (toValue(parts).every(Boolean)) {
        polite(escapeHtmlText(title));
      }
    },
    { immediate: true },
  );
}
