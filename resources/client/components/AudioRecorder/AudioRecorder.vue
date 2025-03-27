<template>
  <div class="p-3 rounded-md bg-white/50">
    <div class="flex items-center justify-between">
      <div class="flex items-center">
        <div
          v-if="isRecording"
          class="w-3 h-3 rounded-full mr-2 bg-red-500 animate-pulse"
        ></div>
        <div class="text-sm font-medium text-neutral-600">
          <template v-if="isRecording">
            {{ formatTime(recordingTime) }}
          </template>
          <template v-else-if="audioBlob"> Recording complete </template>
          <template v-else>
            <p>Record audio</p>
            <small>max 15s</small>
          </template>
        </div>
      </div>
      <RecordButton :isRecording="isRecording" @click="toggleRecording" />
    </div>
    <div v-if="audioBlob && !isRecording" class="mt-3">
      <audio controls :src="audioUrl ?? ''" class="w-full h-10"></audio>

      <div class="flex justify-end gap-2 mt-3">
        <Button variant="secondary" @click="resetRecording"> Discard </Button>

        <Button @click="emitSaveEvent"> Use Recording </Button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useAudioRecorder } from "./useAudioRecorder";
import RecordButton from "./RecordButton.vue";
import Button from "@/components/ui/button/Button.vue";

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
