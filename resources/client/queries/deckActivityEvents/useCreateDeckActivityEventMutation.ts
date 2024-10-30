import { useMutation, useQueryClient } from "@tanstack/vue-query";
import * as api from "@/api";
import { DECKS_QUERY_KEY } from "../queryKeys";

export function useCreateDeckActivityEventMutation() {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: api.createDeckActivityEvent,
    onSuccess: (activityEvent) => {
      queryClient.invalidateQueries({
        queryKey: [DECKS_QUERY_KEY, activityEvent.deck_id],
      });
    },
  });
}
