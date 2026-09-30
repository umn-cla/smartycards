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
      <RecordButton
        ref="recordButton"
        :isRecording="isRecording"
        @click="toggleRecording"
      />
    </div>
    <div v-if="audioBlob && !isRecording" class="mt-3">
      <audio controls :src="audioUrl ?? ''" class="w-full h-10"></audio>

      <div class="flex justify-end gap-2 mt-3">
        <Button variant="secondary" @click="resetRecordingAndFocusRecordButton">
          Discard
        </Button>

        <Button @click="emitSaveEvent"> Use Recording </Button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { type ComponentPublicInstance, ref, watch } from "vue";
import { unrefElement } from "@vueuse/core";
import { useAnnouncer } from "@vue-a11y/announcer";
import { pluralize } from "@/utils/pluralize";
import { useAudioRecorder } from "./useAudioRecorder";
import RecordButton from "./RecordButton.vue";
import Button from "@/components/ui/button/Button.vue";

const emit = defineEmits<{
  save: [{ blob: Blob; url: string; mimeType: string }];
}>();

const {
  isRecording,
  recordingTime,
  audioBlob,
  audioUrl,
  audioMimeType,
  startRecording,
  stopRecording,
  resetRecording,
  formatTime,
} = useAudioRecorder();

const announcer = useAnnouncer();

watch(isRecording, (isNowRecording) => {
  if (isNowRecording) {
    announcer.polite("Recording started.");
    return;
  }
  const seconds = Math.floor(recordingTime.value);
  announcer.polite(
    `Recording stopped after ${seconds} ${pluralize(seconds, "second")}.`,
  );
});

const recordButton = ref<ComponentPublicInstance | null>(null);

function resetRecordingAndFocusRecordButton(): void {
  unrefElement(recordButton)?.focus();
  resetRecording();
}

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
  if (audioBlob.value && audioUrl.value && audioMimeType.value) {
    emit("save", {
      blob: audioBlob.value,
      url: audioUrl.value,
      mimeType: audioMimeType.value,
    });
  }
};
</script>
