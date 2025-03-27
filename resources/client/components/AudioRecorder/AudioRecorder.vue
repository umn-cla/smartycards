<template>
  <div class="p-3 rounded-lg bg-gray-100 shadow-sm">
    <div class="flex items-center justify-between">
      <div class="flex items-center">
        <div
          v-if="isRecording"
          class="w-3 h-3 rounded-full mr-2 bg-red-500 animate-pulse"
        ></div>
        <span class="text-sm font-medium text-neutral-600">
          {{
            isRecording
              ? formatTime(recordingTime)
              : audioBlob
                ? "Recording complete"
                : "Record audio (max 15s)"
          }}
        </span>
      </div>
      <RecordButton :isRecording="isRecording" @click="toggleRecording" />
    </div>
    <div v-if="audioBlob && !isRecording" class="mt-3">
      <audio controls :src="audioUrl ?? ''" class="w-full h-10"></audio>

      <div class="flex justify-end gap-2 mt-3">
        <button
          @click="resetRecording"
          class="px-3 py-1.5 text-sm bg-gray-200 hover:bg-gray-300 text-gray-700 rounded focus:outline-none focus:ring-2 focus:ring-gray-400 transition-colors"
        >
          Discard
        </button>

        <button
          @click="emitSaveEvent"
          class="px-3 py-1.5 text-sm bg-blue-500 hover:bg-blue-600 text-white rounded focus:outline-none focus:ring-2 focus:ring-blue-400 transition-colors"
        >
          Use Recording
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useAudioRecorder } from "./useAudioRecorder";
import RecordButton from "./RecordButton.vue";

const emit = defineEmits<{
  save: [blob: Blob, url: string];
}>();

const {
  isRecording,
  recordingTime,
  audioBlob,
  audioUrl,
  startRecording,
  stopRecording,
  resetRecording,
  formatTime,
} = useAudioRecorder();

const toggleRecording = () => {
  if (isRecording.value) {
    stopRecording();
  } else {
    startRecording().catch((error) => {
      alert(error.message);
    });
  }
};

const emitSaveEvent = () => {
  if (audioBlob.value && audioUrl.value) {
    emit("save", audioBlob.value, audioUrl.value);
  }
};
</script>
