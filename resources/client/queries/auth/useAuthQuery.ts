import { useQuery } from '@tanstack/vue-query';
import * as api from '@/api';
import { PROFILE_QUERY_KEY } from '../queryKeys';

export function useAuthQuery() {
  return useQuery({
    retry: 0,
    queryKey: [PROFILE_QUERY_KEY],
    queryFn: () => api.getCurrentUser({ skipErrorNotifications: true }),
    staleTime: 5 * 60 * 1000, // 5 minutes
  });
}
