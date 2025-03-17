import axios, { AxiosError } from "@/api/axios";
import { ApiError } from "./ApiError";
import type * as T from "@/types";
import { useErrorStore } from "@/stores/useErrorStore";
import config from "@/config";
import { isEmpty } from "ramda";

const NETWORK_ERROR_HTTP_STATUS_CODE = 0;

// this interceptor is used to catch errors from the API
// convert them into API errors and store them in the error store
// so that they're displayed to the user
axios.interceptors.response.use(undefined, async (err: AxiosError) => {
  const customConfig = err.config as T.CustomAxiosRequestConfig;

  const errorStore = useErrorStore();
  let apiError: ApiError;

  if (err.response) {
    // The request was made and the server responded with a status code
    // that falls out of the range of 2xx

    if (err.response.status === 401) {
      // reload page to retrigger server-side auth
      // if we do it server-side, we don't have to worry about the
      // redirect back to the intended page after login
      window.location.reload();

      return;
    }

    const data = (err.response.data as { message?: string }) ?? {};
    const message = data?.message ?? err.message;
    const statusCode = err.response.status;

    apiError = new ApiError(message, statusCode, data);
  } else {
    // Something happened in setting up the request that
    // triggered an Error. This is likely a network error.
    apiError = new ApiError(err.message, NETWORK_ERROR_HTTP_STATUS_CODE);
  }

  if (!customConfig.skipErrorNotifications) {
    // Add the ApiError to the errorStore
    errorStore.setError(apiError);
  }

  return Promise.reject(apiError);
});

export async function csrf() {
  await axios.get(`${config.api.origin}/sanctum/csrf-cookie`);
}

export async function getCurrentUser(
  opts: T.CustomAxiosRequestConfig = {},
): Promise<T.User | null> {
  const res = await axios.get<T.User | null>(`/profile`, opts);
  return isEmpty(res.data) ? null : res.data;
}

export async function getAllDecks() {
  const res = await axios.get<{ data: T.Deck[] }>(`/decks`);
  return res.data.data;
}

export async function getDeckById(deckId: number) {
  const res = await axios.get<{ data: T.DeckWithCards }>(`/decks/${deckId}`);
  return res.data.data;
}

interface CreateDeckParams {
  name: string;
  description: string;
  isTTSEnabled: boolean;
  ttsLocaleFront: string | null;
  ttsLocaleBack: string | null;
}

export async function createDeck(
  deck: CreateDeckParams,
  customConfig: T.CustomAxiosRequestConfig = {},
) {
  await csrf();
  const res = await axios.post<{ data: T.Deck }>(
    `/decks`,
    {
      name: deck.name,
      description: deck.description,
      is_tts_enabled: deck.isTTSEnabled,
      tts_locale_front: deck.ttsLocaleFront,
      tts_locale_back: deck.ttsLocaleBack,
    },
    customConfig,
  );
  return res.data.data;
}

export async function cloneDeck({
  sourceDeckId,
  name,
  description,
  isTTSEnabled,
}: {
  sourceDeckId: T.Deck["id"];
  name: string;
  description: string;
  isTTSEnabled: boolean;
}) {
  const res = await axios.post<{ data: T.Deck }>(
    `/decks/${sourceDeckId}/clone`,
    {
      name,
      description,
      is_tts_enabled: isTTSEnabled,
    },
  );

  return res.data.data;
}

export async function deleteDeck(
  deckId: number,
  customConfig: T.CustomAxiosRequestConfig = {},
) {
  await csrf();
  await axios.delete(`/decks/${deckId}`, customConfig);
}

interface UpdateDeckParams extends CreateDeckParams {
  id: number;
}
export async function updateDeck(deck: UpdateDeckParams) {
  const res = await axios.put<{ data: T.Deck }>(`/decks/${deck.id}`, {
    name: deck.name,
    description: deck.description,
    is_tts_enabled: deck.isTTSEnabled,
    tts_locale_front: deck.ttsLocaleFront,
    tts_locale_back: deck.ttsLocaleBack,
  });
  return res.data.data;
}

export async function createCard(newCardForm: {
  deck_id: number;
  front: T.CardSide;
  back: T.CardSide;
}) {
  const res = await axios.post<{ data: T.Card }>(
    `/decks/${newCardForm.deck_id}/cards`,
    newCardForm,
  );
  return res.data.data;
}

export async function getCardById(cardId: number) {
  const res = await axios.get<{ data: T.Card }>(`/cards/${cardId}`);
  return res.data.data;
}

export async function getCardStatsById(cardId: number) {
  const res = await axios.get<{ data: T.CardStats }>(`/cards/${cardId}/stats`);
  return res.data.data;
}

export async function updateCard(card: T.Card) {
  const res = await axios.put<{ data: T.Card }>(`/cards/${card.id}`, card);
  return res.data.data;
}

export async function deleteCard(card: T.Card) {
  await axios.delete(`/cards/${card.id}`);
}

export async function getAllUserCardAttempts(cardId: number) {
  const res = await axios.get<T.CardAttempt[]>(`/cards/${cardId}/attempts`);
  return res.data;
}

export async function createCardAttempt({
  cardId,
  score,
  promptSide,
}: {
  cardId: number;
  score: number;
  promptSide: T.CardSideName;
}) {
  const res = await axios.post<T.CardAttempt>(`/cards/${cardId}/attempts`, {
    score,
    prompt_side: promptSide,
  });
  return res.data;
}

export async function getDeckMemberships(deckId: number) {
  const res = await axios.get<{ data: T.DeckMembership[] }>(
    `/decks/${deckId}/memberships`,
  );
  return res.data.data;
}

export async function createDeckMembership({
  deckId,
  umndid,
  role,
}: {
  deckId: number;
  umndid: string;
  role: T.DeckMembership["role"];
}) {
  const res = await axios.post<{ data: T.DeckMembership }>(
    `/decks/${deckId}/memberships`,
    {
      umndid,
      role,
    },
  );
  return res.data.data;
}

export async function updateDeckMembership(membership: T.DeckMembership) {
  const res = await axios.put<{ data: T.DeckMembership }>(
    `/memberships/${membership.id}`,
    membership,
  );
  return res.data.data;
}

export async function deleteDeckMembership(membership: T.DeckMembership) {
  await axios.delete(`/memberships/${membership.id}`);
}

export async function leaveDeck(deckId: number) {
  await axios.delete(`/decks/${deckId}/memberships/self`);
}

export async function getDeckShareLink(
  deckId: number,
  permission: "view" | "edit",
  redirectUrl?: string,
) {
  const res = await axios.post<{ url: string }>(
    `/decks/${deckId}/memberships/share`,
    {
      permission,
      redirect_url: redirectUrl,
    },
  );
  return res.data.url;
}

export async function importDeckCards(deckId: number, file: File) {
  const formData = new FormData();
  formData.append("file", file);

  const res = await axios.post<{ data: T.Card[] }>(
    `/decks/${deckId}/import`,
    formData,
    {
      headers: {
        "Content-Type": "multipart/form-data",
      },
    },
  );
  return res.data.data;
}

export async function redirectToLogin(redirect: string) {
  const url = new URL(config.api.loginUrl);
  url.searchParams.set("redirect", redirect);
  window.location.href = url.toString();
}

export async function uploadImageToDeck(deckId: number, file: File) {
  const formData = new FormData();
  formData.append("image", file);

  const res = await axios.post<{ path: string }>(
    `/decks/${deckId}/upload/images`,
    formData,
    {
      headers: {
        "Content-Type": "multipart/form-data",
      },
    },
  );

  return res.data.path;
}

export async function uploadFile(file: File): Promise<T.UploadedFileInfo> {
  const formData = new FormData();
  formData.append("file", file);

  const res = await axios.post<T.UploadedFileInfo>(`files`, formData, {
    headers: {
      "Content-Type": "multipart/form-data",
    },
  });

  return res.data;
}

export async function getAllCommunityDecks() {
  const res = await axios.get<{ data: T.Deck[] }>(`/community/decks`);
  return res.data.data;
}

export async function joinCommunityDeck(deckId: number) {
  await csrf();
  const res = await axios.post(`/community/decks/${deckId}/join`);
  return res.data;
}

export async function createQuizForDeck(
  deckId: number,
  options: T.QuizOptions,
  customConfig: T.CustomAxiosRequestConfig = {},
) {
  await csrf();
  const res = await axios.post<T.Quiz>(
    `/decks/${deckId}/quiz`,
    options,
    customConfig,
  );

  return res.data;
}

export async function regenerateShareLinkForDeck(
  deckId: number,
  permission: "view" | "edit",
  redirectUrl?: string,
) {
  await csrf();
  const res = await axios.post<{ url: string }>(
    `/decks/${deckId}/memberships/share/regenerate`,
    {
      permission,
      redirect_url: redirectUrl,
    },
  );

  return res.data.url;
}

export async function getDeckSummaryReport(deckId: number) {
  const res = await axios.get<T.DeckSummaryReport>(
    `/decks/${deckId}/reports/summary`,
  );
  return res.data;
}

export async function createDeckActivityEvent({
  deckId,
  activityType,
  correctCount,
  totalCount,
}: {
  deckId: T.Deck["id"];
  activityType: T.ActivityTypeName;
  correctCount: number;
  totalCount: number;
}) {
  const res = await axios.post<T.ActivityEvent>(
    `/decks/${deckId}/activity-events`,
    {
      activity_type_name: activityType,
      correct_count: correctCount,
      total_count: totalCount,
    },
  );
  return res.data;
}

export async function getActivityTypes() {
  const res = await axios.get<T.ActivityType[]>(`/activity-types`);
  return res.data;
}

export async function getDeckStats(deckId: number) {
  const res = await axios.get<T.DeckStats>(`/decks/${deckId}/stats`);
  return res.data;
}

export async function getAudioForText(
  text: string,
  lang: string | null,
): Promise<Blob> {
  const res = await axios.post<Blob>(
    `/tts`,
    {
      text,
      lang,
    },
    {
      responseType: "blob",
    },
  );

  return res.data;
}

export async function getFeatureFlags() {
  const res = await axios.get<{ text_to_speech: boolean }>(`/features`);

  return res.data;
}

export { ApiError };
