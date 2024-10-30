import { useQuery } from "@tanstack/vue-query";
import * as api from "@/api";
import type { Ref } from "vue";
import { CARDS_QUERY_KEY, ATTEMPTS_QUERY_KEY } from "../queryKeys";

export function useCardAttemptsQuery(cardId: Ref<number>) {
  return useQuery({
    queryKey: [CARDS_QUERY_KEY, cardId, ATTEMPTS_QUERY_KEY],
    queryFn: () => {
      return api.getAllUserCardAttempts(cardId.value);
    },
  });
}
