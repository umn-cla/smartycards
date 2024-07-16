import { useMutation, useQueryClient } from '@tanstack/vue-query';
import * as api from '@/api';
import { DECKS_QUERY_KEY } from '../queryKeys';

export function useCreateCardMutation() {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: api.createCard,
    onSuccess: (card) => {
      queryClient.invalidateQueries({
        // todo: optimistic updates?
        queryKey: [DECKS_QUERY_KEY, card.deck_id],
      });
    },
  });
}
