<template>
  <div class="p-3 rounded-lg bg-gray-100 shadow-sm">
    <!-- Recording interface -->
    <div class="flex items-center justify-between">
      <!-- Recording status and timer -->
      <div class="flex items-center">
        <div
          v-if="isRecording"
          class="w-3 h-3 rounded-full mr-2 bg-red-500 animate-pulse"
        ></div>
        <div
          v-else-if="isUploading"
          class="w-3 h-3 rounded-full mr-2 bg-blue-500 animate-pulse"
        ></div>
        <span class="text-sm font-medium text-neutral-600">
          <template v-if="isRecording"
            >Recording: {{ formatTime(recordingTime) }}</template
          >
          <template v-else-if="isUploading">Uploading recording...</template>
          <template v-else-if="uploadComplete">Recording saved!</template>
          <template v-else>Record audio (max 15s)</template>
        </span>
      </div>

      <!-- Main record/stop button -->
      <button
        v-if="!isUploading && !uploadComplete"
        @click="toggleRecording"
        class="p-2 rounded-full focus:outline-none focus:ring-2 focus:ring-opacity-50 transition-colors"
        :class="
          isRecording
            ? 'bg-red-500 hover:bg-red-600 focus:ring-red-500 text-white'
            : 'bg-blue-500 hover:bg-blue-600 focus:ring-blue-500 text-white'
        "
        :title="isRecording ? 'Stop recording' : 'Start recording'"
        :disabled="isUploading"
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

      <!-- Success indicator when upload complete -->
      <div v-else-if="uploadComplete" class="p-2 text-green-500">
        <svg
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
            d="M5 13l4 4L19 7"
          />
        </svg>
      </div>

      <!-- Loading spinner when uploading -->
      <div v-else class="p-2 text-blue-500">
        <svg
          class="animate-spin h-5 w-5"
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
        >
          <circle
            class="opacity-25"
            cx="12"
            cy="12"
            r="10"
            stroke="currentColor"
            stroke-width="4"
          ></circle>
          <path
            class="opacity-75"
            fill="currentColor"
            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
          ></path>
        </svg>
      </div>
    </div>

    <!-- Audio preview (after recording) -->
    <div v-if="audioUrl && !isRecording" class="mt-3">
      <audio controls :src="audioUrl" class="w-full h-10"></audio>

      <!-- Action buttons after recording -->
      <div
        v-if="!isUploading && !uploadComplete"
        class="flex justify-end gap-2 mt-3"
      >
        <button
          @click="discardRecording"
          class="px-3 py-1.5 text-sm bg-gray-200 hover:bg-gray-300 text-gray-700 rounded focus:outline-none focus:ring-2 focus:ring-gray-400 transition-colors"
        >
          Discard
        </button>

        <button
          @click="uploadRecording"
          class="px-3 py-1.5 text-sm bg-blue-500 hover:bg-blue-600 text-white rounded focus:outline-none focus:ring-2 focus:ring-blue-400 transition-colors"
        >
          Save Recording
        </button>
      </div>

      <!-- Record new audio button (after successful upload) -->
      <div v-else-if="uploadComplete" class="flex justify-end mt-3">
        <button
          @click="resetAndStartNew"
          class="px-3 py-1.5 text-sm bg-blue-500 hover:bg-blue-600 text-white rounded focus:outline-none focus:ring-2 focus:ring-blue-400 transition-colors"
        >
          Record New Audio
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from "vue";

// Define emits
const emit = defineEmits<{
  (e: "update:modelValue", url: string): void;
}>();

// State variables
const isRecording = ref(false);
const isUploading = ref(false);
const uploadComplete = ref(false);
const recordingTime = ref(0);
const audioBlob = ref<Blob | null>(null);
const audioUrl = ref<string | null>(null);
const mediaRecorder = ref<MediaRecorder | null>(null);
const audioChunks = ref<Blob[]>([]);
const timerInterval = ref<number | null>(null);
const maxRecordingTime = 15; // Maximum recording time in seconds

// Toggle recording (start/stop)
const toggleRecording = () => {
  if (isRecording.value) {
    stopRecording();
  } else {
    startRecording();
  }
};

// Start recording function
const startRecording = async () => {
  try {
    // Reset state
    audioChunks.value = [];
    recordingTime.value = 0;
    uploadComplete.value = false;

    // Request microphone access
    const stream = await navigator.mediaDevices.getUserMedia({
      audio: {
        echoCancellation: true,
        noiseSuppression: true,
        autoGainControl: true,
      },
    });

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

// Discard recording function
const discardRecording = () => {
  if (audioUrl.value) {
    URL.revokeObjectURL(audioUrl.value);
  }

  audioBlob.value = null;
  audioUrl.value = null;
  recordingTime.value = 0;
  uploadComplete.value = false;
};

// Upload recording function
const uploadRecording = async () => {
  if (!audioBlob.value) return;

  try {
    isUploading.value = true;

    // Convert blob to File object
    const file = new File([audioBlob.value], `recording-${Date.now()}.webm`, {
      type: "audio/webm",
    });

    // Create a FormData object to send the file
    const formData = new FormData();
    formData.append("file", file);

    // Use the fetch API to upload the file
    const response = await fetch("/api/upload", {
      method: "POST",
      body: formData,
    });

    if (!response.ok) {
      throw new Error("Upload failed");
    }

    const data = await response.json();

    // Emit the URL to update the parent component
    emit("update:modelValue", data.url);

    // Set upload complete flag
    uploadComplete.value = true;
  } catch (error) {
    console.error("Error uploading recording:", error);
    alert("Failed to upload recording. Please try again.");
  } finally {
    isUploading.value = false;
  }
};

// Reset and start new recording
const resetAndStartNew = () => {
  discardRecording();
};

// Format time display (e.g., 5.2 -> "0:05")
const formatTime = (time: number): string => {
  const minutes = Math.floor(time / 60);
  const seconds = Math.floor(time % 60);
  return `${minutes}:${seconds.toString().padStart(2, "0")}`;
};
</script>
