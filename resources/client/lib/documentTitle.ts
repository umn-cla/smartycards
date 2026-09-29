import {
  computed,
  onBeforeUnmount,
  shallowRef,
  toValue,
  type ComputedRef,
  type MaybeRefOrGetter,
} from "vue";
import { useRoute } from "vue-router";
import type * as T from "@/types";

interface PageDeck {
  deckId: number | null;
  deck: Pick<T.Deck, "id" | "name"> | null | undefined;
}

export interface DocumentTitleState {
  title: string;
  isWaitingForDeck: boolean;
}

const currentPageDeckReader = shallowRef<(() => PageDeck) | null>(null);

function toDocumentTitle(parts: (string | undefined)[]): string {
  const namedParts = parts.filter((part): part is string => Boolean(part));
  return [...namedParts, "SmartyCards"].join(" - ");
}

export function toDocumentTitleState(
  pageTitle: string | undefined,
  pageDeck: PageDeck | undefined,
): DocumentTitleState {
  if (!pageDeck || pageDeck.deckId === null) {
    return { title: toDocumentTitle([pageTitle]), isWaitingForDeck: false };
  }

  const { deck, deckId } = pageDeck;
  if (!deck || deck.id !== deckId) {
    return { title: toDocumentTitle([pageTitle]), isWaitingForDeck: true };
  }

  return {
    title: toDocumentTitle([pageTitle, deck.name]),
    isWaitingForDeck: false,
  };
}

export function useDocumentTitleState(): ComputedRef<DocumentTitleState> {
  const route = useRoute();
  return computed(() =>
    toDocumentTitleState(route.meta.title, currentPageDeckReader.value?.()),
  );
}

export function useDeckDocumentTitle(
  deckId: MaybeRefOrGetter<number | null>,
  deck: MaybeRefOrGetter<Pick<T.Deck, "id" | "name"> | null | undefined>,
): void {
  function readPageDeck(): PageDeck {
    return { deckId: toValue(deckId), deck: toValue(deck) };
  }
  currentPageDeckReader.value = readPageDeck;

  // App.vue's post-flush title watcher runs before this
  // page's onUnmounted, so clearing there announces the
  // next page with this page's deck name.
  onBeforeUnmount(() => {
    if (currentPageDeckReader.value === readPageDeck) {
      currentPageDeckReader.value = null;
    }
  });
}
