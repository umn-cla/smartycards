<template>
  <Toggle
    v-model="isPlaying"
    label="Listen"
    :class="{
      [isIdleClass]: !isPlaying && props.isIdleClass,
    }"
  >
    <IconSound class="size-5" />
  </Toggle>
</template>
<script setup lang="ts">
import { useTextToSpeech } from "@/composables/useTextToSpeech";
import { computed, HTMLAttributes } from "vue";
import { IconSound } from "./icons";
import Toggle from "./Toggle.vue";

const props = defineProps<{
  text: string;
  selectedLanguage: string | null;
  isIdleClass?: HTMLAttributes["class"];
}>();

const textRef = computed(() => props.text);
const selectedLanguageRef = computed(() => props.selectedLanguage);

const { isPlaying } = useTextToSpeech(textRef, selectedLanguageRef);
</script>
<style scoped></style>
