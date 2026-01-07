import { useQuery } from "@tanstack/vue-query";
import * as api from "@/api";
import type { Ref } from "vue";
import { DECKS_QUERY_KEY } from "../queryKeys";

export function useDeckScoresQuery(deckId: Ref<number>) {
  return useQuery({
    queryKey: [DECKS_QUERY_KEY, deckId, "scores"],
    queryFn: () => api.getDeckScores(deckId.value),
  });
}
