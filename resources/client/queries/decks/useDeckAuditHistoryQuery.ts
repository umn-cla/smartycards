import { useQuery } from "@tanstack/vue-query";
import * as api from "@/api";
import type { AuditHistoryParams } from "@/api";
import type { ComputedRef } from "vue";
import { DECKS_QUERY_KEY, REPORTS_QUERY_KEY } from "../queryKeys";

export function useDeckAuditHistoryQuery(
  deckId: ComputedRef<number>,
  params: ComputedRef<AuditHistoryParams>,
) {
  return useQuery({
    queryKey: [DECKS_QUERY_KEY, deckId, REPORTS_QUERY_KEY, "audit-history", params],
    queryFn: () => api.getDeckAuditHistory(deckId.value, params.value),
  });
}
