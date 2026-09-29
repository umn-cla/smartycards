import { computed, toValue, watch, type MaybeRefOrGetter } from "vue";
import { useRoute } from "vue-router";
import type * as T from "@/types";

export function toDocumentTitle(parts: (string | undefined)[]): string {
  const namedParts = parts.filter((part): part is string => Boolean(part));
  return [...namedParts, "SmartyCards"].join(" - ");
}

export function useDeckDocumentTitle(
  deck: MaybeRefOrGetter<Pick<T.Deck, "name"> | null | undefined>,
): void {
  const route = useRoute();
  const pageTitle = route.meta.title;
  const deckName = computed(() => toValue(deck)?.name);
  watch(
    // router.afterEach resets the title on each navigation.
    // Watching deckName alone leaves "Deck - SmartyCards"
    // after moving between two decks with the same name.
    [() => route.fullPath, deckName],
    () => {
      document.title = toDocumentTitle([pageTitle, deckName.value]);
    },
    { immediate: true },
  );
}
