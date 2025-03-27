import { ref, onUnmounted } from "vue";

function getSupportedMimeType() {
  const types = [
    "audio/mp4", // prefer mp4 for safari support
    "audio/webm",
    "audio/ogg;codecs=opus",
    "audio/wav",
  ];

  for (const type of types) {
    if (MediaRecorder.isTypeSupported(type)) {
      return type;
    }
  }

  return null; // No supported type found
}

export function useAudioRecorder(options = { maxDuration: 15 }) {
  const isRecording = ref(false);
  const recordingTime = ref(0);
  const audioBlob = ref<Blob | null>(null);
  const audioUrl = ref<string | null>(null);
  const audioMimeType = ref<string | null>(null);
  const mediaRecorder = ref<MediaRecorder | null>(null);
  const audioChunks = ref<Blob[]>([]);
  const timerInterval = ref<number | null>(null);
  const maxRecordingTime = options.maxDuration;

  const startRecording = async () => {
    try {
      // Reset state
      audioChunks.value = [];
      recordingTime.value = 0;

      // Request microphone access
      const stream = await navigator.mediaDevices.getUserMedia({
        audio: {
          echoCancellation: true,
          noiseSuppression: true,
          autoGainControl: true,
        },
      });

      // detect supported MIME types. Safari, for instance,
      // does not support webm, so we need to use mp4
      audioMimeType.value = getSupportedMimeType();
      if (!audioMimeType.value) {
        throw new Error("No supported MIME type found for recording.");
      }

      mediaRecorder.value = new MediaRecorder(stream, {
        mimeType: audioMimeType.value,
      });

      // while recording, add audio chunks to the audioChunks array
      mediaRecorder.value.ondataavailable = (event) => {
        if (event.data.size > 0) {
          audioChunks.value.push(event.data);
        }
      };

      // When recording stops
      mediaRecorder.value.onstop = () => {
        if (!audioMimeType.value) {
          throw new Error("No supported MIME type found for recording.");
        }

        // create a blob from the audio chunks
        const blob = new Blob(audioChunks.value, { type: audioMimeType.value });
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

      // now, after all this setup, we can actually start recording
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
      throw new Error(
        "Could not access microphone. Please ensure you have granted permission.",
      );
    }
  };

  const stopRecording = () => {
    if (mediaRecorder.value && isRecording.value) {
      mediaRecorder.value.stop();
    }
  };

  const resetRecording = () => {
    if (audioUrl.value) {
      URL.revokeObjectURL(audioUrl.value);
    }

    audioBlob.value = null;
    audioUrl.value = null;
    recordingTime.value = 0;
  };

  const formatTime = (time: number): string => {
    const minutes = Math.floor(time / 60);
    const seconds = Math.floor(time % 60);
    return `${minutes}:${seconds.toString().padStart(2, "0")}`;
  };

  // Clean up
  onUnmounted(() => {
    if (timerInterval.value) {
      clearInterval(timerInterval.value);
    }

    if (audioUrl.value) {
      URL.revokeObjectURL(audioUrl.value);
    }
  });

  return {
    isRecording,
    recordingTime,
    audioBlob,
    audioUrl,
    audioMimeType,
    startRecording,
    stopRecording,
    resetRecording,
    formatTime,
  };
}
