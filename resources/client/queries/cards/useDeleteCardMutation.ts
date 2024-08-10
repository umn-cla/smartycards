import { useMutation, useQueryClient } from '@tanstack/vue-query';
import * as api from '@/api';
import { DECKS_QUERY_KEY } from '../queryKeys';

export function useDeleteCardMutation() {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: api.deleteCard,
    // no data returned from deleteCard
    // get the deck id from the vars
    onSuccess: (_returnedData, card) => {
      queryClient.invalidateQueries({
        queryKey: [DECKS_QUERY_KEY, card.deck_id],
      });
    },
  });
}
