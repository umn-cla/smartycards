import { useMutation, useQueryClient } from "@tanstack/vue-query";
import * as api from "@/api";
import { DECKS_QUERY_KEY, REPORTS_QUERY_KEY } from "../queryKeys";

export function useRetryGradeSubmissionMutation(deckId: number) {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: api.retryGradeSubmission,
    onSuccess: () => {
      queryClient.invalidateQueries({
        queryKey: [DECKS_QUERY_KEY, deckId, REPORTS_QUERY_KEY, "grades"],
      });
    },
  });
}
