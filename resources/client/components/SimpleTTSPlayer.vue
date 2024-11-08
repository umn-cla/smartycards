<template>
  <div>
    <button @click="pause" v-if="isPlaying">
      <IconCirclePause class="size-6" />
      <span class="sr-only">Pause audio</span>
    </button>
    <button @click="play" v-else>
      <IconCirclePlay class="size-6" />
      <span class="sr-only">Play audio</span>
    </button>
  </div>
</template>
<script setup lang="ts">
import { useTextToSpeech } from "@/composables/useTextToSpeech";
import { computed } from "vue";
import { IconCirclePlay, IconCirclePause } from "./icons";
import { stripHtml } from "@/lib/stripHtml";

const props = defineProps<{
  text: string;
  selectedLanguage: string | null;
}>();

const textRef = computed(() => props.text);
const selectedLanguageRef = computed(() => props.selectedLanguage);

const { isPlaying, play, pause } = useTextToSpeech(
  textRef,
  selectedLanguageRef,
);
</script>
<style scoped></style>
