import { useMutation, useQueryClient } from '@tanstack/vue-query';
import * as api from '@/api';
import { DECKS_QUERY_KEY } from '../queryKeys';

export function useUpdateCardMutation() {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: api.updateCard,
    onSuccess: (card) => {
      queryClient.invalidateQueries({
        queryKey: [DECKS_QUERY_KEY, card.deck_id],
      });
    },
  });
}
