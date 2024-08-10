<template>
  <div class="flex flex-col gap-2">
    <div>
      <div v-if="!modelValue">
        <Label>Image</Label>
        <FilePond
          name="image"
          ref="pond"
          labelIdle="Drop files here..."
          acceptedFileTypes="image/jpeg, image/png, image/svg+xml, image/gif"
          :server="{
            process: handleProcessImage,
          }"
          :files="myFiles"
          @init="handleFilePondInit"
        />
        <div class="hidden">
          <Label
            for="image-url"
            class="sr-only"
            >Image Url</Label
          >
          <Input
            :modelValue="modelValue"
            @update:modelValue="$emit('update:modelValue', $event as T.CardSide['content'])"
            placeholder="Image URL"
            class="bg-black/5"
          />
        </div>
      </div>
      <div
        v-else
        class="relative pt-2 pr-2"
      >
        <img
          :src="modelValue"
          alt="Front of card"
          class="block border-4 rounded-lg object-cover flex-1 aspect-video bg-black/5"
        />
        <button
          class="absolute top-0 right-0 bg-neutral-700 hover:bg-neutral-900 text-neutral-100 rounded-full w-6 h-6 flex items-center justify-center transition-colors"
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
import * as T from '@/types';
import { ref } from 'vue';
import config from '@/config';
import * as api from '@/api';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import vueFilePond from 'vue-filepond';
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type';
import { IconX } from '../icons';

import 'filepond/dist/filepond.min.css';
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css';

const props = defineProps<{
  deckId: number;
  modelValue: T.CardSide['content'];
}>();

const emit = defineEmits<{
  (event: 'update:modelValue', value: T.CardSide['content']): void;
}>();

const FilePond = vueFilePond(FilePondPluginFileValidateType);

function handleFilePondInit() {
  console.log('FilePond has initialized');
}

const myFiles = ref<string[]>([]);

async function handleProcessImage(
  fieldName: string,
  file: File,
  metadata: any,
  load: any,
  error: any,
  progress: any,
  abort: any
) {
  const path = await api.uploadImageToDeck(props.deckId, file);

  load(path);

  emit('update:modelValue', `${config.api.origin}/storage/${path}`);

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
