<!-- RecordButton.vue -->
<template>
  <button
    @click="$emit('click')"
    class="p-2 rounded-full transition-colors border-2"
    :class="
      isRecording
        ? 'bg-brand-orange-500 hover:bg-brand-orange-500/75 text-white'
        : 'bg-brand-maroon-800/5 hover:bg-brand-maroon-800 hover:text-white text-brand-maroon-800 border-brand-maroon-800/10'
    "
    :title="isRecording ? 'Stop recording' : 'Start recording'"
  >
    <!-- Microphone icon (when not recording) -->
    <IconMicrophone v-if="!isRecording" class="size-6" />
    <IconStop v-else class="size-6" />
  </button>
</template>

<script setup lang="ts">
import { IconMicrophone, IconStop } from "@/components/icons";

defineProps<{
  isRecording: boolean;
}>();

defineEmits<{
  (event: "click"): void;
}>();
</script>
