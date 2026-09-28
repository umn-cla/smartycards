import { toValue, type MaybeRefOrGetter } from "vue";
import { useRoute } from "vue-router";
import { useTitle } from "@vueuse/core";
import type * as T from "@/types";

export function toDocumentTitle(parts: (string | undefined)[]): string {
  const namedParts = parts.filter((part): part is string => Boolean(part));
  return [...namedParts, "SmartyCards"].join(" - ");
}

export function useDeckDocumentTitle(
  deck: MaybeRefOrGetter<Pick<T.Deck, "name"> | null | undefined>,
): void {
  const pageTitle = useRoute().meta.title;
  useTitle(() => toDocumentTitle([pageTitle, toValue(deck)?.name]), {
    restoreOnUnmount: false,
  });
}
