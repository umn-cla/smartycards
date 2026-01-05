import { computed, ref, type ComputedRef } from "vue";
import { useDeckByIdQuery } from "@/queries/decks";
import { useDeckStatsQuery } from "@/queries/decks/useDeckStatsQuery";
import { useCreateDeckActivityEventMutation } from "@/queries/deckActivityEvents/useCreateDeckActivityEventMutation";
import * as T from "@/types";

interface UsePracticeDeckOptions {
  deckId: ComputedRef<number>;
  ltiLaunchId?: ComputedRef<string | null>;
}

/**
 * Composable for managing practice deck state and actions
 */
export function usePracticeDeck(options: UsePracticeDeckOptions) {
  const { deckId, ltiLaunchId } = options;

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
      ...(ltiLaunchId?.value && { ltiLaunchId: ltiLaunchId.value }),
    });

    if (ltiLaunchId?.value) {
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
