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
  is_public: boolean;
  current_user_role: MembershipRole | null; // could be null if public deck

  current_user_details: {
    user_id: User["id"];
    role: MembershipRole | null; // could be null if viewing public deck
    xp: number;
    last_activity_at: ISODateTime | null;
  };

  current_user_last_activity_at: ISODateTime; //current user's last activity

  current_user_xp: number; // current user's xp

  // current user capabilities
  capabilities: {
    canUpdate: boolean;
    canDelete: boolean;
    canViewMemberships: boolean;
    canCreateMembership: boolean;
    canLeave: boolean;
    canJoinAsViewer: boolean; // can join if not already a member, and deck is public
    canViewReports: boolean;
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
  deck_id: Deck["id"];
  created_at: ISODateTime;
  updated_at: ISODateTime;
  attempts_count: number;
  last_attempted_at: ISODateTime;
  avg_score: number | null;
}

export interface UserCardScore {
  id: number;
  userId: User["id"];
  deckId: Deck["id"];
  cardId: Card["id"];
  score: number;
  attempts: number;
  createdAt: ISODateTime;
  updatedAt: ISODateTime;
}

export interface CardAttempt {
  id: number;
  card_id: Card["id"];
  user_id: User["id"];
  deck_id: Deck["id"];
  score: number;
  created_at: ISODateTime;
  updated_at: ISODateTime;
}

export interface NavMenuItem {
  name: string;
  to?: RouteLocationRaw;
  href?: string;
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
  deck_id: Deck["id"];
  user_id: User["id"];
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

interface MemberParticipationStats {
  user: User;
  has_attempted_all_cards: boolean;
  has_quiz_activity: boolean;
  has_matching_activity: boolean;
}

interface CardWithGlobalStats extends Card {
  attempts_count: number;
  attempts_avg_score: number;
}

export interface DeckSummaryReport {
  cards_count: number;
  memberships_count: number;
  cards_with_stats: CardWithGlobalStats[];
  memberships_with_stats: MemberParticipationStats[];
}

export enum ActivityTypeName {
  CREATE_CARD = "CREATE_CARD",
  PRACTICE_CARD = "PRACTICE_CARD",
  PRACTICE_ALL_CARDS = "PRACTICE_ALL_CARDS",
  QUIZ = "QUIZ",
  MATCHING = "MATCHING",
}

export interface ActivityEvent {
  id: number;
  user_id: User["id"];
  deck_id: Deck["id"];
  type: ActivityTypeName;
  xp: number;
  created_at: ISODateTime;
  updated_at: ISODateTime;
}

export interface ActivityType {
  id: number;
  name: ActivityTypeName;
  label: string;
  description: string;
  default_xp: number;
}

export interface DeckStats {
  cards_count: number;
  memberships_count: number;
  current_user_xp: number;
}
