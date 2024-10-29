import { useQuery } from "@tanstack/vue-query";
import * as api from "@/api";
import type { Ref } from "vue";
import { DECKS_QUERY_KEY, STATS_QUERY_KEY } from "../queryKeys";

export function useDeckStatsQuery(deckId: Ref<number>) {
  return useQuery({
    queryKey: [DECKS_QUERY_KEY, deckId.value, STATS_QUERY_KEY],
    queryFn: () => api.getDeckStats(deckId.value),
  });
}
