import { type Ref, type InjectionKey } from "vue";

export const MAX_TTS_CHARS = 200;

export const IS_DECK_TTS_ENABLED_INJECTION_KEY: InjectionKey<Ref<boolean>> =
  Symbol("isDeckTtsEnabled");
