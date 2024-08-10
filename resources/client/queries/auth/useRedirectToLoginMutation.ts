import { useMutation } from '@tanstack/vue-query';
import * as api from '@/api';

export function useRedirectToLoginMutation() {
  return useMutation({
    mutationFn: () => {
      if (window.location.pathname !== '/auth/login') {
        return api.redirectToLogin(window.location.pathname);
      }

      return api.redirectToLogin('/decks');
    },
  });
}
