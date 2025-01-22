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
          process: handleProcessImage,
        }"
        :files="myFiles"
        class="focus-within:ring-2 focus-within:ring-blue-600"
      />
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
      <Label :for="makeInputId('image-url')" class="sr-only">Image Url</Label>
      <Input
        :id="makeInputId('image-url')"
        :modelValue="modelValue"
        @update:modelValue="$emit('update:modelValue', $event as string)"
        placeholder="Image URL"
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

import "filepond/dist/filepond.min.css";
import "filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css";
import { useMakeInputId } from "@/composables/useMakeInputId";

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
const isValidUrlComputed = computed(() => isValidUrl(props.modelValue));

function onFileChange(file: File) {
  return api.uploadFile(file);
}

async function handleProcessImage(
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
</script>
<style>
.filepond--root.filepond--hopper {
  margin-bottom: 0;
}
.filepond--drop-label {
  background: hsla(0, 0%, 0%, 0.05);
  border-radius: 1rem;
  border: 1px dashed hsla(0, 0%, 0%, 0.2);
}
.filepond--credits {
  display: none;
}
.filepond--drop-label label {
  color: hsla(0, 0%, 0%, 0.25);
}
</style>
