import { useCreateDeckActivityEventMutation } from "@/queries/deckActivityEvents/useCreateDeckActivityEventMutation";
import { useDeckByIdQuery } from "@/queries/decks";
import { useDeckStatsQuery } from "@/queries/decks/useDeckStatsQuery";
import * as T from "@/types";
import { ref, type ComputedRef } from "vue";

interface UsePracticeDeckOptions {
  deckId: ComputedRef<number>;
  isLtiContext?: ComputedRef<boolean>;
}

/**
 * Composable for managing practice deck state and actions
 */
export function usePracticeDeck(options: UsePracticeDeckOptions) {
  const { deckId, isLtiContext } = options;

  const initialSideName = ref<T.CardSideName | "random">("front");
  const hasCompletedPractice = ref(false);

  const { data: deck, isLoading: isDeckLoading } = useDeckByIdQuery(deckId);
  const { data: deckStats } = useDeckStatsQuery(deckId);
  const { mutate: createActivityEvent } = useCreateDeckActivityEventMutation();

  async function handlePracticeComplete(cardCount: number) {
    await createActivityEvent({
      deckId: deck.value?.id ?? 0,
      activityType: T.ActivityTypeName.PRACTICE_ALL_CARDS,
      correctCount: cardCount,
      totalCount: cardCount,
    });

    if (isLtiContext?.value) {
      hasCompletedPractice.value = true;
    }
  }

  function handleResetPractice() {
    hasCompletedPractice.value = false;
  }

  return {
    initialSideName,
    deck,
    isDeckLoading,
    deckStats,
    hasCompletedPractice,
    handlePracticeComplete,
    handleResetPractice,
  };
}
