<template>
  <div
    class="rounded-lg p-2 flex flex-col gap-4 font-serif text-xl border border-black/20"
  >
    <slot name="prepend" />
    <div
      class="flex items-center justify-center flex-col gap-4 flex-1 overflow-y-auto scrollbar-thin scrollbar-thumb-black/10 scrollbar-track-transparent"
    >
      <template v-for="block in contentBlocks" :key="block.id">
        <div
          v-if="isTextBlock(block)"
          v-html="block.content"
          @click.stop
          class="text-center"
        />
        <img
          v-else-if="isImageBlock(block)"
          :src="block.content"
          :alt="block.meta?.alt"
          class="rounded-sm w-full object-contain"
        />
        <EmbedVideo v-else-if="isEmbedBlock(block)" :src="block.content" />
        <audio
          v-else-if="isAudioBlock(block)"
          :src="block.content"
          controls
        ></audio>
        <RevealBlockView
          v-else-if="isRevealBlock(block)"
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
import RevealBlockView from "./RevealBlockView.vue";

const props = defineProps<{
  label?: string;
  side: T.CardSide;
}>();

const contentBlocks = computed(() => props.side);

// Type guard functions
function isImageBlock(block: T.ContentBlock): block is T.ImageContentBlock {
  return block.type === "image";
}

function isTextBlock(block: T.ContentBlock): block is T.TextContentBlock {
  return block.type === "text";
}

function isEmbedBlock(block: T.ContentBlock): block is T.EmbedContentBlock {
  return block.type === "embed";
}

function isAudioBlock(block: T.ContentBlock): block is T.AudioContentBlock {
  return block.type === "audio";
}

function isRevealBlock(block: T.ContentBlock): block is T.RevealContentBlock {
  return block.type === "reveal";
}
</script>
<style></style>
