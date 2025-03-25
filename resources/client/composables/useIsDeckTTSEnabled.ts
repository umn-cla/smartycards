import { computed, MaybeRefOrGetter, toValue } from "vue";
import type { Deck } from "@/types";
import config from "@/config";

/*
 * Provides the isDeckTTSEnabled ref to the component tree.
 * to avoid prop drilling
 */
export const useIsDeckTTSEnabled = (
  deck: MaybeRefOrGetter<Deck | null | undefined>,
) => {
  const isDeckTTSEnabled = computed(
    () =>
      // global tts feature flag is enabled
      config.features.isTTSEnabled &&
      // and deck has tts enabled
      (toValue(deck)?.is_tts_enabled ?? false),
  );

  return {
    isDeckTTSEnabled,
  };
};
