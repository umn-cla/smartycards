import { computed, MaybeRefOrGetter, provide, toValue, type Ref } from "vue";

import { Deck } from "@/types";
import { useAllFeatureFlagsQuery } from "@/queries/featureFlags";

/*
 * Provides the isDeckTTSEnabled ref to the component tree.
 * to avoid prop drilling
 */
export const useIsDeckTTSEnabled = (
  deck: MaybeRefOrGetter<Deck | null | undefined>,
) => {
  const { data: featureFlags } = useAllFeatureFlagsQuery();

  const isDeckTTSEnabled = computed(
    () =>
      // global tts feature flag is enabled
      (featureFlags.value?.text_to_speech ?? false) &&
      // and deck has tts enabled
      (toValue(deck)?.is_tts_enabled ?? false),
  );

  return {
    isDeckTTSEnabled,
  };
};
