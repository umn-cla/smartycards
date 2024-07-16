import { useQuery } from '@tanstack/vue-query';
import * as api from '@/api';
import { DECKS_QUERY_KEY } from '../queryKeys';

export function useAllDecksQuery() {
  return useQuery({
    queryKey: [DECKS_QUERY_KEY],
    queryFn: api.getAllDecks,
  });
}
