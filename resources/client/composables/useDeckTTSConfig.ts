import { computed, MaybeRefOrGetter, toValue } from "vue";

import { Deck } from "@/types";
import { useAllFeatureFlagsQuery } from "@/queries/featureFlags";

/*
 * Provides the isDeckTTSEnabled ref to the component tree.
 * to avoid prop drilling
 */
export const useDeckTTSConfig = (
  deck: MaybeRefOrGetter<Deck | null | undefined>,
) => {
  const { data: featureFlags } = useAllFeatureFlagsQuery();

  const isTTSEnabled = computed(
    (): boolean =>
      // global tts feature flag is enabled
      (featureFlags.value?.text_to_speech ?? false) &&
      // and deck has tts enabled
      (toValue(deck)?.is_tts_enabled ?? false),
  );

  const ttsLocaleFront = computed(
    (): string | null => toValue(deck)?.tts_locale_front ?? null,
  );
  const ttsLocaleBack = computed(
    (): string | null => toValue(deck)?.tts_locale_back ?? null,
  );

  return {
    isTTSEnabled,
    ttsLocaleFront,
    ttsLocaleBack,
  };
};
