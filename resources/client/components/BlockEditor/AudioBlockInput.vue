<template>
  <div class="p-2">
    <div v-if="!modelValue">
      <Label class="sr-only">Audio</Label>
      <FilePond
        name="audio"
        ref="pond"
        labelIdle="Add audio file (.mp3, .ogg, .m4a, .aac, .midi)"
        acceptedFileTypes="audio/mpeg,audio/ogg,audio/vorbis,audio/mp4,audio/aac,audio/midi,audio/x-m4a,audio/m4a"
        :server="{
          process: handleProcessImage,
        }"
        :files="myFiles"
        @init="handleFilePondInit"
      />
      <p class="text-neutral-400 text-xs text-center">— or —</p>
      <div>
        <Label class="sr-only" for="image-url">Audio Url</Label>
        <Input
          :modelValue="modelValue"
          @update:modelValue="$emit('update:modelValue', $event as string)"
          placeholder="Audio URL"
          class="bg-brand-maroon-800/5"
        />
      </div>
    </div>
    <div v-else class="flex items-center justify-center">
      <div class="relative pt-2 pr-2">
        <audio controls :src="modelValue" class="block border-4 rounded-lg">
          Your browser does not support the
          <code>audio</code> element.
        </audio>
        <button
          class="absolute top-0 right-0 bg-neutral-700 hover:bg-brand-maroon-800 text-neutral-100 rounded-full w-6 h-6 flex items-center justify-center transition-colors"
          @click="$emit('update:modelValue', '')"
        >
          <IconX />
          <span class="sr-only">Clear</span>
        </button>
      </div>
    </div>
  </div>
</template>
<script setup lang="ts">
import { ref } from "vue";
import * as api from "@/api";
import { Label } from "@/components/ui/label";
import { Input } from "@/components/ui/input";
import vueFilePond from "vue-filepond";
import FilePondPluginFileValidateType from "filepond-plugin-file-validate-type";
import { IconX } from "../icons";

import "filepond/dist/filepond.min.css";
import "filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css";

defineProps<{
  modelValue: string;
}>();

const emit = defineEmits<{
  (event: "update:modelValue", value: string): void;
}>();

const FilePond = vueFilePond(FilePondPluginFileValidateType);

function handleFilePondInit() {
  console.log("FilePond has initialized");
}

const myFiles = ref<string[]>([]);

function onFileChange(file: File) {
  return api.uploadFile(file);
}

async function handleProcessImage(
  _fieldName: string,
  file: File,
  _metadata: any,
  load: any,
  _error: any,
  _progress: any,
  abort: any,
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
