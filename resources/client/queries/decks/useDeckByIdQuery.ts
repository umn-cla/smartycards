import { useQuery } from '@tanstack/vue-query';
import * as api from '@/api';
import type { Ref } from 'vue';
import { DECKS_QUERY_KEY } from '../queryKeys';

export function useDeckByIdQuery(deckId: Ref<number>) {
  return useQuery({
    queryKey: [DECKS_QUERY_KEY, deckId],
    queryFn: () => api.getDeckById(deckId.value),
  });
}
