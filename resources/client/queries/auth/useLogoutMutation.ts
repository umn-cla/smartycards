import { useMutation, useQueryClient } from '@tanstack/vue-query';
import * as api from '@/api';

export function useLogoutMutation() {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: api.logout,
    onSuccess: () => {
      queryClient.clear();
    },
  });
}
