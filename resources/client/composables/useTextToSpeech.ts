// composables/useTextToSpeech.ts
import {
  ref,
  onUnmounted,
  watch,
  type MaybeRefOrGetter,
  toValue,
  toRef,
  ComputedRef,
} from "vue";
import * as api from "@/api";

export function useTextToSpeech(
  text: MaybeRefOrGetter<string>,
  lang?: MaybeRefOrGetter<string>,
) {
  const DEFAULT_LANG = "en-US";
  const blobUrl = ref<string | null>(null);
  const audio = new Audio();

  async function getAudioUrl(): Promise<string> {
    const blob = await api.getAudioForText(
      toValue(text),
      toValue(lang) ?? DEFAULT_LANG,
    );
    return URL.createObjectURL(blob);
  }

  async function play() {
    blobUrl.value ??= await getAudioUrl();
    audio.src = blobUrl.value;
    audio.play();
  }

  function cleanup() {
    if (blobUrl.value) {
      URL.revokeObjectURL(blobUrl.value);
      blobUrl.value = null;
    }

    audio.pause();
    audio.src = "";
  }

  // Clean up blob URL when text changes
  const textRef = toRef(text);
  const langRef = toRef(lang);
  watch([textRef, langRef], cleanup);

  // Clean up on component unmount
  onUnmounted(cleanup);

  return {
    blobUrl,
    play,
    cleanup,
    audio,
  };
}
