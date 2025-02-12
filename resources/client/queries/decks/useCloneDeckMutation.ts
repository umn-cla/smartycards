import { useMutation, useQueryClient } from "@tanstack/vue-query";
import * as api from "@/api";
import { DECKS_QUERY_KEY } from "../queryKeys";

export function useCloneDeckMutation() {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: api.cloneDeck,
    onSuccess: () => {
      queryClient.invalidateQueries({
        queryKey: [DECKS_QUERY_KEY],
      });
    },
  });
}
