import { useMutation, useQueryClient } from "@tanstack/vue-query";
import * as api from "@/api";
import {
  CARDS_QUERY_KEY,
  ATTEMPTS_QUERY_KEY,
  DECKS_QUERY_KEY,
  STATS_QUERY_KEY,
} from "../queryKeys";

export function useCreateCardAttemptMutation() {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: api.createCardAttempt,
    onSuccess: (attempt) => {
      queryClient.invalidateQueries({
        queryKey: [CARDS_QUERY_KEY, attempt.card_id, ATTEMPTS_QUERY_KEY],
      });

      queryClient.invalidateQueries({
        queryKey: [DECKS_QUERY_KEY, attempt.deck_id, STATS_QUERY_KEY],
      });
    },
  });
}
