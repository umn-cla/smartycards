<template>
  <div class="">
    <InputGroup
      :id="`embedUrl`"
      label="Embed Url"
      required
      v-model="model"
      placeholder="https://www.youtube.com/embed/VIDEO_ID"
      class="my-4"
      inputClass="bg-black/5"
    />
    <EmbedVideo
      v-if="isValidUrlComputed"
      :src="model"
    />
  </div>
</template>
<script setup lang="ts">
import InputGroup from '@/components/InputGroup.vue';
import EmbedVideo from '@/components/EmbedVideo.vue';
import * as T from '@/types';
import { isValidUrl } from '@/lib/utils';
import { computed } from 'vue';

const model = defineModel<T.CardSide['content']>({ required: true });

const isValidUrlComputed = computed(() => isValidUrl(model.value));
</script>
<style scoped></style>
