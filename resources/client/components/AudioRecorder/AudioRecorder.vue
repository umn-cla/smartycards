<template>
  <div class="p-5 rounded-lg bg-gray-100 max-w-md mx-auto shadow-md">
    <div class="flex items-center mb-4">
      <div
        class="w-3 h-3 rounded-full mr-2"
        :class="isRecording ? 'bg-red-500 animate-pulse' : 'bg-teal-500'"
      ></div>
      <span class="text-sm font-medium">
        {{
          isRecording
            ? `Recording: ${formatTime(recordingTime)}`
            : "Ready to record"
        }}
      </span>
    </div>

    <div class="flex flex-wrap gap-2 mb-4">
      <button
        @click="startRecording"
        :disabled="isRecording || audioBlob !== null"
        class="px-3 py-2 bg-green-500 text-white rounded-md font-medium text-sm transition-colors hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50 disabled:opacity-50 disabled:cursor-not-allowed"
      >
        <span class="sr-only">Start Recording</span>
        <IconRecord />
      </button>

      <button
        @click="stopRecording"
        :disabled="!isRecording"
        class="px-3 py-2 bg-red-500 text-white rounded-md font-medium text-sm transition-colors hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50 disabled:opacity-50 disabled:cursor-not-allowed"
      >
        Stop Recording
      </button>

      <button
        @click="resetRecording"
        :disabled="!audioBlob"
        class="px-3 py-2 bg-gray-500 text-white rounded-md font-medium text-sm transition-colors hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50 disabled:opacity-50 disabled:cursor-not-allowed"
      >
        Reset
      </button>

      <button
        @click="saveRecording"
        :disabled="!audioBlob"
        class="px-3 py-2 bg-blue-500 text-white rounded-md font-medium text-sm transition-colors hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 disabled:opacity-50 disabled:cursor-not-allowed"
      >
        Save Recording
      </button>
    </div>

    <audio v-if="audioUrl" controls :src="audioUrl" class="w-full mt-3"></audio>
  </div>
</template>

<script setup lang="ts">
import { ref, onUnmounted } from "vue";
import IconRecord from "../icons/IconRecord.vue";

// State variables
const isRecording = ref(false);
const recordingTime = ref(0);
const audioBlob = ref<Blob | null>(null);
const audioUrl = ref<string | null>(null);
const mediaRecorder = ref<MediaRecorder | null>(null);
const audioChunks = ref<Blob[]>([]);
const timerInterval = ref<number | null>(null);
const maxRecordingTime = 15; // Maximum recording time in seconds

// Start recording function
const startRecording = async () => {
  try {
    // Reset state
    audioChunks.value = [];
    recordingTime.value = 0;

    // Request microphone access
    const stream = await navigator.mediaDevices.getUserMedia({ audio: true });

    // Create new MediaRecorder
    mediaRecorder.value = new MediaRecorder(stream);

    // Event handler for data available
    mediaRecorder.value.ondataavailable = (event) => {
      if (event.data.size > 0) {
        audioChunks.value.push(event.data);
      }
    };

    // Event handler for when recording stops
    mediaRecorder.value.onstop = () => {
      // Create blob from recorded chunks
      const blob = new Blob(audioChunks.value, { type: "audio/webm" });
      audioBlob.value = blob;

      // Create URL for audio playback
      audioUrl.value = URL.createObjectURL(blob);

      // Stop all tracks in the stream to release the microphone
      stream.getTracks().forEach((track) => track.stop());

      // Clear the timer
      if (timerInterval.value) {
        clearInterval(timerInterval.value);
        timerInterval.value = null;
      }

      isRecording.value = false;
    };

    // Start recording
    mediaRecorder.value.start();
    isRecording.value = true;

    // Set up timer for recording duration
    timerInterval.value = window.setInterval(() => {
      recordingTime.value += 0.1;

      // Auto-stop if max recording time is reached
      if (recordingTime.value >= maxRecordingTime && isRecording.value) {
        stopRecording();
      }
    }, 100);
  } catch (error) {
    console.error("Error starting recording:", error);
    alert(
      "Could not access microphone. Please ensure you have granted permission.",
    );
  }
};

// Stop recording function
const stopRecording = () => {
  if (mediaRecorder.value && isRecording.value) {
    mediaRecorder.value.stop();
  }
};

// Reset recording function
const resetRecording = () => {
  audioBlob.value = null;
  audioUrl.value = null;
  recordingTime.value = 0;
};

// Save recording function
const saveRecording = () => {
  if (audioBlob.value) {
    const timestamp = new Date().toISOString().replace(/[:.]/g, "-");
    const a = document.createElement("a");
    a.href = audioUrl.value as string;
    a.download = `recording-${timestamp}.webm`;
    a.click();
  }
};

// Format time display (e.g., 5.2 -> "5.2s")
const formatTime = (time: number): string => {
  return `${time.toFixed(1)}s`;
};

// Clean up on component unmount
onUnmounted(() => {
  if (timerInterval.value) {
    clearInterval(timerInterval.value);
  }

  if (audioUrl.value) {
    URL.revokeObjectURL(audioUrl.value);
  }
});
</script>
