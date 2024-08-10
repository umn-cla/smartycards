import { useQuery } from '@tanstack/vue-query';
import * as api from '@/api';
import { type Ref } from 'vue';
import { CARDS_QUERY_KEY } from '../queryKeys';

export function useCardByIdQuery(cardId: Ref<number | null>) {
  return useQuery({
    queryKey: [CARDS_QUERY_KEY, cardId],
    queryFn: () => (cardId.value ? api.getCardById(cardId.value) : Promise.resolve(null)),
    initialData: null,
  });
}
