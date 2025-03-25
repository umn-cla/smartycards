<template>
  <Toggle
    v-model="isPlaying"
    label="Listen"
    :disabled="isEmpty"
    :class="{
      [isIdleClass]: !isPlaying && props.isIdleClass,
      '!opacity-25 cursor-not-allowed': isEmpty,
    }"
    data-cy="simple-tts-player"
  >
    <IconSound class="size-5" />
    <span class="sr-only">Listen</span>
    <span
      data-cy="simple-tts-player-language"
      v-if="selectedLanguage"
      class="text-xs"
      :class="{
        'text-brand-maroon-800/50': !isPlaying,
        'sr-only': selectedLanguage === 'auto',
      }"
    >
      {{ languageName }}
    </span>
  </Toggle>
</template>
<script setup lang="ts">
import { useTextToSpeech } from "@/composables/useTextToSpeech";
import { computed, HTMLAttributes } from "vue";
import { IconSound } from "./icons";
import Toggle from "./Toggle.vue";
import { ttsLanguageOptions as languages } from "@/lib/ttsLanguageOptions";

const props = defineProps<{
  text: string;
  selectedLanguage: string | null;
  isIdleClass?: HTMLAttributes["class"];
}>();

const textRef = computed(() => props.text);
const selectedLanguageRef = computed(() => props.selectedLanguage);

const { isPlaying, isEmpty } = useTextToSpeech(textRef, selectedLanguageRef);

const languageName = computed(() => {
  const lang = languages.find((l) => l.locale === props.selectedLanguage);
  return lang?.name;
});
</script>
<style scoped></style>
