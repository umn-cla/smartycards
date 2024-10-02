import { useMutation, useQueryClient } from "@tanstack/vue-query";
import * as api from "@/api";
import {
  DECKS_QUERY_KEY,
  MEMBERSHIPS_QUERY_KEY,
  SHARE_QUERY_KEY,
} from "../queryKeys";
import { Ref } from "vue";

export function useRegenerateDeckShareLinkMutation(
  deckId: Ref<number>,
  permission: "view" | "edit",
) {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: () => api.regenerateShareLinkForDeck(deckId.value, permission),
    onSuccess: (newUrl) => {
      const queryKey = [
        DECKS_QUERY_KEY,
        deckId.value,
        MEMBERSHIPS_QUERY_KEY,
        SHARE_QUERY_KEY,
        permission,
      ];

      queryClient.setQueryData(queryKey, newUrl);
    },
  });
}
