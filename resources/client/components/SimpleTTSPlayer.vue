<template>
  <div>
    <Toggle v-model="isPlaying" label="Listen">
      <IconSound class="size-5" />
    </Toggle>
  </div>
</template>
<script setup lang="ts">
import { useTextToSpeech } from "@/composables/useTextToSpeech";
import { computed } from "vue";
import { IconSound } from "./icons";
import Toggle from "./Toggle.vue";

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
