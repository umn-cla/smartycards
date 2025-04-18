import {
  ComputedRef,
  MaybeRefOrGetter,
  provide,
  computed,
  toValue,
  type InjectionKey,
  type Ref,
  toRef,
  inject,
} from "vue";
import { ttsLanguageOptions } from "@/lib/ttsLanguageOptions";
import config from "@/config";

import * as T from "@/types";

interface TTSContext {
  deck: Ref<T.Deck | null | undefined>;
  cardSideName: Ref<T.CardSideName>;
  isTTSEnabled: Ref<boolean>;
  defaultLanguageOption: ComputedRef<T.LanguageOption>;
  languageOptions: T.LanguageOption[];
}

const TTS_CONTEXT_KEY: InjectionKey<TTSContext> = Symbol("ttsContext");

/**
 * provide deck and card side information for the component
 * tree to avoid prop drilling. e.g. card side's default TTS
 */
export function provideTTSContext(
  deck: MaybeRefOrGetter<T.Deck | null | undefined>,
  cardSideName: MaybeRefOrGetter<T.CardSideName>,
) {
  const isTTSEnabled = computed(
    (): boolean =>
      // global tts feature flag is enabled
      config.features.isTTSEnabled &&
      // and deck has tts enabled
      (toValue(deck)?.is_tts_enabled ?? false),
  );

  const defaultLanguageOption = computed(
    (): T.LanguageOption => getDefaultLanguageOption(deck, cardSideName),
  );

  const ttsContext: TTSContext = {
    deck: toRef(deck),
    cardSideName: toRef(cardSideName),
    isTTSEnabled,
    defaultLanguageOption,
    languageOptions: ttsLanguageOptions,
  };

  provide(TTS_CONTEXT_KEY, ttsContext);
}

/**
 * use provided deck and card side information
 * from the component tree
 */
export function useTTSContext() {
  const ttsContext = inject(TTS_CONTEXT_KEY);

  if (!ttsContext) {
    throw new Error(
      "useTTSContext must be used within a parent provider. Use `provideTTSContext()` to provide the context.",
    );
  }

  return ttsContext;
}

/**
 * determin the default language used for TTS
 * based on the deck and card side
 */
function getDefaultLanguageOption(
  deck: MaybeRefOrGetter<T.Deck | null | undefined>,
  side: MaybeRefOrGetter<T.CardSideName | null | undefined>,
): T.LanguageOption {
  const autoOption = { name: "Auto", locale: "auto" };

  if (!toValue(deck) || !toValue(side)) {
    return autoOption;
  }

  const defaultLocaleForSide =
    toValue(side) === "front"
      ? toValue(deck)?.tts_locale_front
      : toValue(deck)?.tts_locale_back;

  if (!defaultLocaleForSide) {
    return autoOption;
  }

  return (
    ttsLanguageOptions.find(
      (option) => option.locale === defaultLocaleForSide,
    ) ?? autoOption
  );
}
