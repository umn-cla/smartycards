import { useQuery } from "@tanstack/vue-query";
import * as api from "@/api";
import type { Ref } from "vue";
import { DECKS_QUERY_KEY } from "../queryKeys";

export function useUserAssignmentsQuery(deckId: Ref<number>) {
  return useQuery({
    queryKey: [DECKS_QUERY_KEY, deckId, "assignments"],
    queryFn: () => api.getUserAssignments(deckId.value),
  });
}
