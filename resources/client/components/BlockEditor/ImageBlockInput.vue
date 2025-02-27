<template>
  <div class="p-2" data-cy="image-block-input">
    <div v-if="!modelValue">
      <Label class="sr-only" :for="makeInputId('image-drop')">Image</Label>
      <FilePond
        :id="makeInputId('image-drop')"
        name="image"
        ref="pond"
        labelIdle="Add an image"
        acceptedFileTypes="image/jpeg, image/png, image/svg+xml, image/gif"
        :server="{
          process: handleProcessImage,
        }"
        :files="myFiles"
        class="focus-within:ring-2 focus-within:ring-blue-600"
      />
    </div>
    <div v-else class="relative pt-2 pr-2">
      <img
        v-if="isValidUrlComputed"
        :src="modelValue"
        alt="Front of card"
        class="block border-4 rounded-lg object-contain w-full flex-1 bg-brand-maroon-800/5"
      />
      <div
        v-else
        class="h-16 flex items-center justify-center bg-brand-maroon-800/5 rounded-lg"
      >
        <p class="text-neutral-400 text-sm">Invalid image URL</p>
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
        data-cy="image-url-text-input"
      />
    </div>
    <div>
      <Label class="sr-only" :for="makeInputId('alt-text')">Alt Text</Label>
      <Input
        :id="makeInputId('alt-text')"
        :modelValue="meta.alt"
        @update:modelValue="
          $emit('update:meta', { ...meta, alt: $event as string })
        "
        placeholder="Alt text"
        class="bg-brand-maroon-800/5"
      />
    </div>
  </div>
</template>
<script setup lang="ts">
import { computed, ref } from "vue";
import * as api from "@/api";
import { Label } from "@/components/ui/label";
import { Input } from "@/components/ui/input";
import vueFilePond from "vue-filepond";
import FilePondPluginFileValidateType from "filepond-plugin-file-validate-type";
import { IconX } from "../icons";
import "filepond/dist/filepond.min.css";
import "filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css";
import { isValidUrl } from "@/lib/utils";
import * as T from "@/types";
import { useMakeInputId } from "@/composables/useMakeInputId";

const props = defineProps<{
  id: T.ContentBlock["id"];
  modelValue: string;
  meta: {
    alt: string;
  };
}>();

const emit = defineEmits<{
  (event: "update:modelValue", value: string): void;
  (event: "update:meta", value: { alt: string }): void;
}>();

const { makeInputId } = useMakeInputId("image-block-input", props.id);

const FilePond = vueFilePond(FilePondPluginFileValidateType);
const isValidUrlComputed = computed(() => isValidUrl(props.modelValue));

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

.filepond--root .filepond--drop-label {
  background: hsla(0, 0%, 100%, 0.15);
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
