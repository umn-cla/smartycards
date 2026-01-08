import { useQuery } from "@tanstack/vue-query";
import * as api from "@/api";
import type { Ref } from "vue";
import { DECKS_QUERY_KEY } from "../queryKeys";

export function useDeckAssignmentsQuery(deckId: Ref<number>) {
  return useQuery({
    queryKey: [DECKS_QUERY_KEY, deckId, "assignments"],
    queryFn: () => api.getAssignmentsForDeck(deckId.value),
  });
}
