import { useQuery } from "@tanstack/vue-query";
import * as api from "@/api";
import { DECKS_QUERY_KEY } from "../queryKeys";

export function useCommunityDecksQuery() {
  return useQuery({
    queryKey: [DECKS_QUERY_KEY, { filter: "community" }],
    queryFn: api.getAllCommunityDecks,
    initialData: [],
  });
}
