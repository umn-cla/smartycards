import { useQuery } from "@tanstack/vue-query";
import * as api from "@/api";
import type { Ref } from "vue";
import { DECKS_QUERY_KEY, REPORTS_QUERY_KEY } from "../queryKeys";

export function useDeckGradesReportQuery(deckId: Ref<number>) {
  return useQuery({
    queryKey: [DECKS_QUERY_KEY, deckId, REPORTS_QUERY_KEY, "grades"],
    queryFn: () => api.getDeckGradesReport(deckId.value),
    refetchInterval: 10000, // 10 seconds for queue status updates
  });
}
