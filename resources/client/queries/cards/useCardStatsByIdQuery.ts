import { useQuery } from '@tanstack/vue-query';
import * as api from '@/api';
import { type Ref } from 'vue';
import { CARDS_QUERY_KEY } from '../queryKeys';

export function useCardStatsByIdQuery(cardId: Ref<number | null>) {
  return useQuery({
    queryKey: [CARDS_QUERY_KEY, cardId, 'stats'],
    queryFn: () => (cardId.value ? api.getCardStatsById(cardId.value) : Promise.resolve(null)),
    initialData: null,
  });
}
