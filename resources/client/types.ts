import { type AxiosRequestConfig } from 'axios';
import type { RouteLocationRaw } from 'vue-router';

/**
 * declare what things might be on the global window object here
 */
declare global {
  interface Window {
    katex: any;
  }
}

export type CSSClass = string | Record<string, boolean> | CSSClass[];
export type ISODateTime = string;

export interface CustomAxiosRequestConfig extends AxiosRequestConfig {
  skipErrorNotifications?: boolean;
}

export interface User {
  id: number;
  name: string;
  email: string;
  umndid: string;
  emplid: string;
  first_name: string;
  last_name: string;
  isAdmin?: boolean;
}

type MembershipRole = 'viewer' | 'editor' | 'owner';

export interface DeckMembership {
  id: number;
  user_id: number;
  deck_id: number;
  user: User;
  role: MembershipRole;
  status: 'active' | 'pending';
  created_at: ISODateTime;
  updated_at: ISODateTime;
  capabilities: {
    canUpdate: boolean;
    canDelete: boolean;
  };
}

export interface Deck {
  id: number;
  name: string;
  description: string;
  cards?: Card[];
  cards_count?: number;
  memberships_count?: number;
  current_user_role: MembershipRole;
  avg_score: number; // average card score for current user
  last_attempted_at: ISODateTime; // latest card attempt for current user
  // current user capabilities
  capabilities: {
    canUpdate: boolean;
    canDelete: boolean;
    canViewMemberships: boolean;
    canCreateMembership: boolean;
    canLeave: boolean;
  };
  created_at: ISODateTime;
  updated_at: ISODateTime;
}

export interface DeckWithCards extends Deck {
  cards: Card[];
}

type UUID = string;

export type CardSideName = 'front' | 'back';

export interface CardSide {
  type: 'image' | 'text' | 'audio' | 'embed';
  content: string;
  meta: {
    hint: string;
    alt?: string;
  };
}

export interface Card {
  id: number;
  front: CardSide;
  back: CardSide;
  deck_id: string;
  created_at: ISODateTime;
  updated_at: ISODateTime;
  attempts_count: number;
  last_attempted_at: ISODateTime;
  avg_score: number;
}

export interface UserCardScore {
  id: number;
  userId: number;
  deckId: string;
  cardId: string;
  score: number;
  attempts: number;
  createdAt: ISODateTime;
  updatedAt: ISODateTime;
}

export interface CardAttempt {
  id: number;
  card_id: number;
  user_id: number;
  deck_id: number;
  score: number;
  created_at: ISODateTime;
  updated_at: ISODateTime;
}

export interface LDAPUser {
  umndid: string;
  email: string;
  internet_id: string;
  display_name: string;
}

export interface NavMenuItem {
  name: string;
  to: RouteLocationRaw;
  icon?: any;
}
