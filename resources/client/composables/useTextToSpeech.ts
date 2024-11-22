// composables/useTextToSpeech.ts
import {
  ref,
  onUnmounted,
  watch,
  type MaybeRefOrGetter,
  toValue,
  toRef,
  computed,
} from "vue";
import * as api from "@/api";

export function useTextToSpeech(
  text: MaybeRefOrGetter<string>,
  lang?: MaybeRefOrGetter<string | null>,
) {
  const blobUrl = ref<string | null>(null);
  const audio = new Audio();
  const audioState = ref<"playing" | "paused" | "idle">("idle");
  const isPlaying = computed({
    get() {
      return audioState.value === "playing";
    },
    set(val: boolean) {
      val ? play() : pause();
    },
  });

  const isPaused = computed(() => audioState.value === "paused");
  const isIdle = computed(() => audioState.value === "idle");

  async function getAudioUrl(): Promise<string> {
    const blob = await api.getAudioForText(toValue(text).trim(), toValue(lang));
    return URL.createObjectURL(blob);
  }

  function pause() {
    audioState.value = "paused";
    audio.pause();
  }

  async function play() {
    audioState.value = "playing";
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

  // audio handlers
  const onPlay = () => (audioState.value = "playing");
  const onPause = () => (audioState.value = "paused");
  const onEnded = () => (audioState.value = "idle");
  audio.addEventListener("play", onPlay);
  audio.addEventListener("pause", onPause);
  audio.addEventListener("ended", onEnded);

  // clean up audio handlers on component unmount
  onUnmounted(() => {
    audio.removeEventListener("play", onPlay);
    audio.removeEventListener("pause", onPause);
    audio.removeEventListener("ended", onEnded);
  });

  return {
    blobUrl,
    play,
    pause,
    isPlaying,
    isPaused,
    isIdle,
    audioState,
  };
}
