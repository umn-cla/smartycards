<template>
  <div>
    <InputGroup
      :id="`audioUrl`"
      label="Audio Url"
      required
      v-model="model"
      placeholder="https://example.com/audio.mp3"
      class="my-4"
      inputClass="bg-black/5"
    />

    <audio
      v-if="isValidUrlComputed"
      controls
      :src="model"
      class="w-full p-2 border border-neutral-400 rounded"
    >
      Your browser does not support the
      <code>audio</code> element.
    </audio>
  </div>
</template>
<script setup lang="ts">
import * as T from '@/types';
import InputGroup from '@/components/InputGroup.vue';
import { isValidUrl } from '@/lib/utils';
import { computed } from 'vue';

const model = defineModel<T.CardSide['content']>({ required: true });

const isValidUrlComputed = computed(() => isValidUrl(model.value));
</script>
<style scoped></style>
