import { useQuery } from '@tanstack/vue-query';
import * as api from '@/api';
import type { Ref } from 'vue';
import { DECKS_QUERY_KEY, MEMBERSHIPS_QUERY_KEY } from '../queryKeys';

export function useDeckMembershipsQuery(deckId: Ref<number>) {
  return useQuery({
    queryKey: [DECKS_QUERY_KEY, deckId, MEMBERSHIPS_QUERY_KEY],
    queryFn: () => api.getDeckMemberships(deckId.value),
  });
}
