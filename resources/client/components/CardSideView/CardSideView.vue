<template>
  <div class="rounded-lg p-2 flex flex-col gap-4 border border-black/20">
    <h2 class="text-xs uppercase text-center text-black/30" v-if="showLabel">
      {{ props.label }}
    </h2>
    <slot name="prepend" />
    <div
      class="flex flex-col gap-4 flex-1 overflow-y-auto scrollbar-thin scrollbar-thumb-black/10 scrollbar-track-transparent"
    >
      <template v-for="block in contentBlocks" :key="block.id">
        <TextBlockView
          v-if="isTextBlock(block)"
          :block="block"
          :class="{
            'flex-1 flex items-center justify-center':
              contentBlocks.length === 1,
          }"
        />
        <ImageBlockView
          v-else-if="isImageBlock(block)"
          :src="block.content"
          :alt="block.meta?.alt ?? ''"
        />
        <VideoBlockView v-else-if="isVideoBlock(block)" :block="block" />
        <EmbedVideo v-else-if="isEmbedBlock(block)" :src="block.content" />
        <AudioPlayer
          v-else-if="isAudioBlock(block)"
          :src="block.content"
          :class="{
            'flex-1 flex items-center justify-center':
              contentBlocks.length === 1,
          }"
        />
        <HintBlockView
          v-else-if="isHintBlock(block)"
          :modelValue="block.content"
          :meta="block.meta"
        />
      </template>
    </div>
    <slot name="append" />
  </div>
</template>
<script setup lang="ts">
import * as T from "@/types";
import { computed } from "vue";
import EmbedVideo from "@/components/EmbedVideo.vue";
import HintBlockView from "./HintBlockView.vue";
import AudioPlayer from "@/components/AudioPlayer.vue";
import ImageBlockView from "@/components/CardSideView/ImageBlockView.vue";
import TextBlockView from "./TextBlockView.vue";
import VideoBlockView from "./VideoBlockView.vue";

const props = withDefaults(
  defineProps<{
    label?: string;
    side: T.CardSide;
    showLabel?: boolean;
  }>(),
  {},
);

const contentBlocks = computed(() => props.side);

// Type guard functions
function isImageBlock(block: T.ContentBlock): block is T.ImageContentBlock {
  return block.type === "image";
}

function isTextBlock(block: T.ContentBlock): block is T.TextContentBlock {
  return block.type === "text";
}

function isVideoBlock(block: T.ContentBlock): block is T.VideoContentBlock {
  return block.type === "video";
}

function isEmbedBlock(block: T.ContentBlock): block is T.EmbedContentBlock {
  return block.type === "embed";
}

function isAudioBlock(block: T.ContentBlock): block is T.AudioContentBlock {
  return block.type === "audio";
}

function isHintBlock(block: T.ContentBlock): block is T.HintContentBlock {
  return block.type === "hint";
}
</script>
<style></style>
