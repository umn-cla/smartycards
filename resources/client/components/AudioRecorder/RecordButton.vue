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
    <svg
      v-if="!isRecording"
      xmlns="http://www.w3.org/2000/svg"
      class="h-5 w-5"
      fill="none"
      viewBox="0 0 24 24"
      stroke="currentColor"
    >
      <path
        stroke-linecap="round"
        stroke-linejoin="round"
        stroke-width="2"
        d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"
      />
    </svg>

    <!-- Stop icon (when recording) -->
    <svg
      v-else
      xmlns="http://www.w3.org/2000/svg"
      class="h-5 w-5"
      fill="none"
      viewBox="0 0 24 24"
      stroke="currentColor"
    >
      <path
        stroke-linecap="round"
        stroke-linejoin="round"
        stroke-width="2"
        d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
      />
      <path
        stroke-linecap="round"
        stroke-linejoin="round"
        stroke-width="2"
        d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"
      />
    </svg>
  </button>
</template>

<script setup lang="ts">
defineProps<{
  isRecording: boolean;
}>();

defineEmits<{
  (e: "click"): void;
}>();
</script>
