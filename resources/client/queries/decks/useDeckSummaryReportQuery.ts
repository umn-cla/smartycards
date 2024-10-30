import { useQuery } from "@tanstack/vue-query";
import * as api from "@/api";
import type { Ref } from "vue";
import { DECKS_QUERY_KEY, REPORTS_QUERY_KEY } from "../queryKeys";

export function useDeckSummaryReportQuery(deckId: Ref<number>) {
  return useQuery({
    queryKey: [DECKS_QUERY_KEY, deckId, REPORTS_QUERY_KEY, "summary"],
    queryFn: () => api.getDeckSummaryReport(deckId.value),
  });
}
