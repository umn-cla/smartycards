<template>
  <div class="bg-black/5 rounded-lg p-4 flex items-center justify-center flex-col gap-2">
    <template
      v-for="block in contentBlocks"
      :key="block.id"
    >
      <div
        v-if="isTextBlock(block)"
        v-html="block.content"
      />
      <img
        v-else-if="isImageBlock(block)"
        :src="block.content"
        :alt="block.meta?.alt"
        class="rounded-sm w-full h-full object-contain"
      />
      <EmbedVideo
        v-else-if="isEmbedBlock(block)"
        :src="block.content"
      />
      <audio
        v-else-if="isAudioBlock(block)"
        :src="block.content"
        controls
      ></audio>
    </template>
  </div>
</template>
<script setup lang="ts">
import * as T from '@/types';
import { computed } from 'vue';
import EmbedVideo from './EmbedVideo.vue';

const props = defineProps<{
  label?: string;
  side: T.CardSide;
}>();

const contentBlocks = computed(() => props.side);

// Type guard functions
function isImageBlock(block: T.ContentBlock): block is T.ImageContentBlock {
  return block.type === 'image';
}

function isTextBlock(block: T.ContentBlock): block is T.TextContentBlock {
  return block.type === 'text';
}

function isEmbedBlock(block: T.ContentBlock): block is T.EmbedContentBlock {
  return block.type === 'embed';
}

function isAudioBlock(block: T.ContentBlock): block is T.AudioContentBlock {
  return block.type === 'audio';
}
</script>
<style></style>
