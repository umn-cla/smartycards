<template>
  <div class="p-2">
    <div v-if="!modelValue">
      <Label class="sr-only" :for="makeInputId('audio-drop')">Audio</Label>
      <FilePond
        :id="makeInputId('audio-drop')"
        name="audio"
        ref="pond"
        labelIdle="Add audio file (.mp3, .ogg, .m4a, .aac, .midi)"
        acceptedFileTypes="audio/mpeg,audio/ogg,audio/vorbis,audio/mp4,audio/aac,audio/midi,audio/x-m4a,audio/m4a"
        :server="{
          process: handleProcessAudio,
        }"
        :files="myFiles"
        class="focus-within:ring-2 focus-within:ring-blue-600"
      />

      <p class="text-neutral-400 text-xs text-center mt-4">— or —</p>

      <!-- Only show the audio recorder if no audio is set -->
      <div v-if="!isUploading">
        <AudioRecorder @save="handleRecordingComplete" />
      </div>
      <div
        v-else
        class="p-3 bg-gray-100 rounded-lg shadow-sm flex items-center justify-center"
      >
        <div class="flex items-center">
          <svg
            class="animate-spin h-5 w-5 mr-2 text-blue-500"
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
          <span class="text-sm font-medium text-neutral-600"
            >Uploading recording...</span
          >
        </div>
      </div>
    </div>

    <div v-else class="relative pt-2 pr-2">
      <audio
        v-if="isValidUrlComputed"
        controls
        :src="modelValue"
        class="block border-4 rounded-lg"
      >
        Your browser does not support the
        <code>audio</code> element.
      </audio>
      <div
        v-else
        class="h-12 flex items-center justify-center bg-brand-maroon-800/5 rounded-lg"
      >
        <p class="text-neutral-400 text-sm">Invalid audio URL</p>
      </div>
      <button
        class="absolute top-0 right-0 bg-neutral-700 hover:bg-brand-maroon-800 text-neutral-100 rounded-full w-6 h-6 flex items-center justify-center transition-colors"
        @click="$emit('update:modelValue', '')"
      >
        <IconX />
        <span class="sr-only">Clear</span>
      </button>
    </div>

    <p class="text-neutral-400 text-xs text-center mt-4">— or —</p>
    <div class="mb-2">
      <Label :for="makeInputId('image-url')" class="sr-only">Audio Url</Label>
      <Input
        :id="makeInputId('audio-url')"
        :modelValue="modelValue"
        @update:modelValue="$emit('update:modelValue', $event as string)"
        placeholder="Audio URL"
        class="bg-brand-maroon-800/5"
      />
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from "vue";
import * as api from "@/api";
import { ContentBlock } from "@/types";
import { Label } from "@/components/ui/label";
import { Input } from "@/components/ui/input";
import vueFilePond from "vue-filepond";
import FilePondPluginFileValidateType from "filepond-plugin-file-validate-type";
import { IconX } from "../icons";
import { isValidUrl } from "@/lib/utils";
import AudioRecorder from "../AudioRecorder/AudioRecorder.vue";

import "filepond/dist/filepond.min.css";
import "filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css";
import { useMakeInputId } from "@/composables/useMakeInputId";
import { useErrorStore } from "@/stores/useErrorStore";

const props = defineProps<{
  id: ContentBlock["id"];
  modelValue: string;
}>();

const emit = defineEmits<{
  (event: "update:modelValue", value: string): void;
}>();

const { makeInputId } = useMakeInputId("audio-block-input", props.id);
const FilePond = vueFilePond(FilePondPluginFileValidateType);

const myFiles = ref<string[]>([]);
const isUploading = ref(false);
const isValidUrlComputed = computed(() => isValidUrl(props.modelValue));

function onFileChange(file: File) {
  return api.uploadFile(file);
}

async function handleProcessAudio(
  _fieldName: string,
  file: File,
  _metadata: unknown,
  load: (url: string) => void,
  _error: unknown,
  _progress: unknown,
  abort: unknown,
) {
  const fileInfo = await onFileChange(file);

  load(fileInfo.url);

  emit("update:modelValue", fileInfo.url);

  return { abort };
}

const errorStore = useErrorStore();

async function handleRecordingComplete(blob: Blob, url: string) {
  try {
    isUploading.value = true;

    // Convert blob to File object
    const file = new File([blob], `recording-${Date.now()}.webm`, {
      type: "audio/webm",
    });

    // Upload the file to the server
    const fileInfo = await onFileChange(file);

    // Update the model value with the new URL
    emit("update:modelValue", fileInfo.url);

    // Clean up the temporary object URL
    URL.revokeObjectURL(url);
  } catch (error) {
    console.error("Error uploading recording:", error);
    errorStore.setError(error as Error);
  } finally {
    isUploading.value = false;
  }
}
</script>
<style>
.filepond--root .filepond--drop-label {
  background: #f8f2ea;
  border-radius: 0.5rem;
}
</style>
