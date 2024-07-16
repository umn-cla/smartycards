import { useMutation, useQueryClient } from '@tanstack/vue-query';
import * as api from '@/api';
import { CARDS_QUERY_KEY, ATTEMPTS_QUERY_KEY } from '../queryKeys';

export function useCreateCardAttemptMutation() {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: api.createCardAttempt,
    onSuccess: (attempt) => {
      console.log('attempt', attempt);
      queryClient.invalidateQueries({
        queryKey: [CARDS_QUERY_KEY, attempt.card_id, ATTEMPTS_QUERY_KEY],
      });
    },
  });
}
