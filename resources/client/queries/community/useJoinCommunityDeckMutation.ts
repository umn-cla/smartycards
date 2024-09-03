import { useMutation, useQueryClient } from "@tanstack/vue-query";
import * as api from "@/api";
import { DECKS_QUERY_KEY } from "../queryKeys";

export function useJoinCommunityDeckMutation() {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: api.joinCommunityDeck,
    onSuccess: () => {
      queryClient.invalidateQueries({
        queryKey: [DECKS_QUERY_KEY],
      });
    },
  });
}
