import { useQuery } from "@tanstack/vue-query";
import * as api from "@/api";
import type { Ref } from "vue";
import { DECKS_QUERY_KEY, REPORTS_QUERY_KEY } from "../queryKeys";

export function useDeckAuditHistoryQuery(deckId: Ref<number>, page: Ref<number>) {
  return useQuery({
    queryKey: [DECKS_QUERY_KEY, deckId, REPORTS_QUERY_KEY, "audit-history", page],
    queryFn: () => api.getDeckAuditHistory(deckId.value, page.value),
  });
}
