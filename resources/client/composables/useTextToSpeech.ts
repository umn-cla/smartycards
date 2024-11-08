// composables/useTextToSpeech.ts
import { ref, onUnmounted, watch, type Ref } from "vue";
import * as api from "@/api";

export function useTextToSpeech(text: Ref<string>, lang?: Ref<string>) {
  const DEFAULT_LANG = "en-US";
  const blobUrl = ref<string | null>(null);
  const audio = new Audio();

  async function getAudioUrl(): Promise<string> {
    const blob = await api.getAudioForText(
      text.value,
      lang?.value ?? DEFAULT_LANG,
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
  watch(text, cleanup);

  // Clean up on component unmount
  onUnmounted(cleanup);

  return {
    blobUrl,
    play,
    cleanup,
    audio,
  };
}
