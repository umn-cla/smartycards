<!-- RecordButton.vue -->
<template>
  <button
    @click="$emit('click')"
    class="p-2 rounded-full focus:outline-none focus:ring-2 focus:ring-opacity-50 transition-colors"
    :class="
      isRecording
        ? 'bg-red-500 hover:bg-red-600 focus:ring-red-500 text-white'
        : 'bg-blue-500 hover:bg-blue-600 focus:ring-blue-500 text-white'
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
