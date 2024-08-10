import { useMutation, useQueryClient } from '@tanstack/vue-query';
import * as api from '@/api';
import { DECKS_QUERY_KEY, MEMBERSHIPS_QUERY_KEY } from '../queryKeys';

export function useUpdateDeckMembershipMutation() {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: api.updateDeckMembership,
    onSuccess: (membership) => {
      queryClient.invalidateQueries({
        queryKey: [DECKS_QUERY_KEY, membership.deck_id, MEMBERSHIPS_QUERY_KEY],
      });
    },
  });
}
