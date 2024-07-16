<template>
  <div class="bg-black/5 rounded-lg p-4 flex items-center justify-center">
    <div
      v-if="side.type === 'text'"
      v-html="side.content"
    />
    <img
      v-else-if="side.type === 'image'"
      :src="side.content"
      :alt="altText"
      class="object-cover rounded-md w-full h-full max-h-40"
    />
    <EmbedVideo
      v-else-if="side.type === 'embed'"
      :src="side.content"
    />
    <audio
      v-else-if="side.type === 'audio'"
      :src="side.content"
      controls
    ></audio>
  </div>
</template>
<script setup lang="ts">
import * as T from '@/types';
import { computed } from 'vue';
import EmbedVideo from './EmbedVideo.vue';

const props = defineProps<{
  label: string;
  side: T.CardSide;
}>();

const altText = computed(() => (props.side.meta?.alt as string) || 'Preview Image');
</script>
<style></style>
