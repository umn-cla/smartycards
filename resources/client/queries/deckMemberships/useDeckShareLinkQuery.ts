import { useQuery } from "@tanstack/vue-query";
import * as api from "@/api";
import type { Ref } from "vue";
import {
  DECKS_QUERY_KEY,
  MEMBERSHIPS_QUERY_KEY,
  SHARE_QUERY_KEY,
} from "../queryKeys";

export function useDeckShareLinkQuery(
  deckId: Ref<number>,
  permission: "view" | "edit",
  redirectUrl?: string,
) {
  // should this be a mutate because it's a POST request?
  return useQuery({
    queryKey: [
      DECKS_QUERY_KEY,
      deckId,
      MEMBERSHIPS_QUERY_KEY,
      SHARE_QUERY_KEY,
      { permission, redirectUrl },
    ],
    queryFn: () => api.getDeckShareLink(deckId.value, permission, redirectUrl),
  });
}
