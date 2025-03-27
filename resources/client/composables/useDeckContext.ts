import { provide, inject, toRef, type MaybeRefOrGetter, type Ref } from "vue";
import * as T from "@/types";

interface DeckContext {
  deck: Ref<T.Deck>;
}

const DECK_CONTEXT_KEY = Symbol("deckContext");

export function provideDeckContext(deck: MaybeRefOrGetter<T.Deck>) {
  const deckContext: DeckContext = {
    deck: toRef(deck),
  };

  provide(DECK_CONTEXT_KEY, deckContext);
}

export function useDeckContext() {
  return inject(DECK_CONTEXT_KEY) as DeckContext;
}
