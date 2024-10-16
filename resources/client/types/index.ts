import { type AxiosRequestConfig } from "axios";
import type { RouteLocationRaw } from "vue-router";

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
export type HTMLString = string;

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

type MembershipRole = "viewer" | "editor" | "owner";

export interface DeckMembership {
  id: number;
  user_id: number;
  deck_id: number;
  user: User;
  role: MembershipRole;
  status: "active" | "pending";
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
  current_user_role: MembershipRole | null; // could be null if public deck
  avg_score: number; // average card score for current user
  last_attempted_at: ISODateTime; // latest card attempt for current user
  // current user capabilities
  capabilities: {
    canUpdate: boolean;
    canDelete: boolean;
    canViewMemberships: boolean;
    canCreateMembership: boolean;
    canLeave: boolean;
    canJoinAsViewer: boolean; // can join if not already a member, and deck is public
  };
  created_at: ISODateTime;
  updated_at: ISODateTime;
}

export interface DeckWithCards extends Deck {
  cards: Card[];
}

type UUID = string;

export type CardSideName = "front" | "back";

export type CardSide = ContentBlock[];

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

export interface NavMenuItem {
  name: string;
  to: RouteLocationRaw;
  icon?: any;
}

export type ContentBlockType =
  | "text"
  | "image"
  | "audio"
  | "video"
  | "embed"
  | "hint"
  | "math";

export interface ContentBlock {
  id: UUID;
  type: ContentBlockType;
  content: unknown;
  meta: Record<string, unknown> | null;
}

export interface TextContentBlock extends ContentBlock {
  type: "text";
  content: HTMLString;
}

export interface ImageContentBlock extends ContentBlock {
  type: "image";
  content: string; // url
  meta: {
    alt: string;
  };
}

export interface AudioContentBlock extends ContentBlock {
  type: "audio";
  content: string; // url
}

export interface EmbedContentBlock extends ContentBlock {
  type: "embed";
  content: string; // url
}

export interface HintContentBlock extends ContentBlock {
  type: "hint";
  content: string;
  meta: {
    label: string;
  };
}

export interface VideoContentBlock extends ContentBlock {
  type: "video";
  content: string; // url
}

export interface MathContentBlock extends ContentBlock {
  type: "math";
  content: string; // latex
}

export interface UploadedFileInfo {
  name: string;
  path: string; // /storage/files/${name}
  url: string;
  mime_type: string;
  size: number;
}

export interface CardStats {
  id: number; // card id
  deck_id: number;
  user_id: number;
  attempts_count: number;
  avg_score: number;
  last_attempted_at: ISODateTime;
}

export interface QuizQuestion {
  sourceCardId: number;
  sourceCard: Card;
  sourceCardSide: CardSideName; // side the prompt is based on
  prompt: string;
  choices: string[];
  correctChoiceIndex: number;
}

export interface Quiz {
  questions: QuizQuestion[];
}

export interface QuizOptions {
  cardSide: CardSideName;
  numberOfQuestions: number;
}
