import { defineStore } from 'pinia';
import type { User, Deck, CustomAxiosRequestConfig } from '@/types';
import * as api from '@/api';
import config from '@/config';
import { useRouter } from 'vue-router';

interface AppStoreState {
  isInitialized: boolean;
  currentUser: User | null;
  decks: Deck[];
}

export const useAppStore = defineStore({
  id: 'app',
  state: (): AppStoreState => ({
    isInitialized: false,
    currentUser: null,
    decks: [],
  }),
  getters: {
    isLoggedIn: (state) => !!state.currentUser?.id,
    getDeck: (state) => (id: number) => {
      return state.decks.find((deck) => deck.id === id);
    },
  },
  actions: {
    resetAppState() {
      this.isInitialized = false;
      this.currentUser = null;
      this.decks = [];
    },
    redirectToLogin() {
      const url = new URL(config.api.loginUrl);
      url.searchParams.set('redirect', window.location.href);
      window.location.href = url.toString();
    },
    logout() {
      api.logout();
      const router = useRouter();
      router.push('/');
    },
    async init() {
      [this.currentUser, this.decks] = await Promise.all([api.getCurrentUser(), api.getAllDecks()]);
      this.isInitialized = true;
    },
    async createDeck(
      form: { name: string; description: string },
      customConfig: CustomAxiosRequestConfig = {}
    ) {
      const deck = await api.createDeck(form, customConfig);
      this.decks.push(deck);
    },
    async deleteDeck(deckId: number, customConfig: CustomAxiosRequestConfig = {}) {
      await api.deleteDeck(deckId, customConfig);
      this.decks = this.decks.filter((deck) => deck.id !== deckId);
    },
  },
});
