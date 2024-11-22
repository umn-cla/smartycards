<template>
  <Toggle
    v-model="isPlaying"
    label="Listen"
    :class="{
      [isIdleClass]: !isPlaying && props.isIdleClass,
    }"
  >
    <IconSound class="size-5" />
    <span
      v-if="languageName"
      class="text-xs"
      :class="{
        'text-brand-maroon-800/50': !isPlaying,
      }"
    >
      {{ languageName }}</span
    >
  </Toggle>
</template>
<script setup lang="ts">
import { useTextToSpeech } from "@/composables/useTextToSpeech";
import { computed, HTMLAttributes } from "vue";
import { IconSound } from "./icons";
import Toggle from "./Toggle.vue";
import { getTTSLanguageOptions } from "@/lib/getTtsLanguageOptions";

const props = defineProps<{
  text: string;
  selectedLanguage: string | null;
  isIdleClass?: HTMLAttributes["class"];
}>();

const textRef = computed(() => props.text);
const selectedLanguageRef = computed(() => props.selectedLanguage);

const { isPlaying } = useTextToSpeech(textRef, selectedLanguageRef);

const languages = getTTSLanguageOptions();
const languageName = computed(() => {
  const lang = languages.find((l) => l.locale === props.selectedLanguage);
  return lang?.name;
});
</script>
<style scoped></style>
