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
    const isFirstDeckRequestPending =
      isFetchingTitleDeck.value && titleDeckFailureCount.value === 0;
    return {
      title: toDocumentTitle([route.meta.title, titleDeck.value?.name]),
      isWaitingForDeck: titleDeckId.value !== null && isFirstDeckRequestPending,
    };
  });
}
