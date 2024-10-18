<template>
  <div
    class="bg-brand-oatmeal-50 shadow-sm border border-brand-oatmeal-300 rounded-sm p-1 aspect-square overflow-y-auto scrollbar-thin scrollbar-thumb-black/10 scrollbar-track-transparent items-center relative"
  >
    <!-- <Button
      variant="secondary"
      class="absolute top-1 right-1 z-20 bg-neutral-50"
      >Zoom</Button
    > -->
    <div class="flex flex-col h-full w-full items-center justify-center">
      <template v-for="block in blocks" :key="block.id">
        <TextBlockView
          v-if="isTextBlock(block)"
          :block="block"
          class="text-xs sm:text-base !leading-[1] pointer-events-none"
        />
        <ImageBlockView
          v-else-if="isImageBlock(block)"
          :src="block.content"
          :alt="block.meta?.alt ?? ''"
          :withLightbox="false"
        />
        <VideoBlockView v-else-if="isVideoBlock(block)" :block="block" />
        <EmbedBlockView v-else-if="isEmbedBlock(block)" :block="block" />
        <!-- <AudioBlockView v-else-if="isAudioBlock(block)" :block="block" /> -->
        <SimpleAudioPlayer
          v-else-if="isAudioBlock(block)"
          :src="block.content"
        />
        <HintBlockView
          v-else-if="isHintBlock(block)"
          :modelValue="block.content"
          :meta="block.meta"
        />
        <MathBlockView v-else-if="isMathBlock(block)" :block="block" />
        <UnknownBlockView v-else :block="block" />
      </template>
    </div>
  </div>
</template>
<script setup lang="ts">
import * as T from "@/types";
import { computed } from "vue";
import { Button } from "@/components/ui/button";
import {
  isImageBlock,
  isTextBlock,
  isVideoBlock,
  isAudioBlock,
  isEmbedBlock,
  isMathBlock,
  isHintBlock,
} from "@/lib/isBlockOfType";
import MathBlockView from "@/components/CardSideView/MathBlockView.vue";
import HintBlockView from "@/components/CardSideView/HintBlockView.vue";
import ImageBlockView from "@/components/CardSideView/ImageBlockView.vue";
import TextBlockView from "@/components/CardSideView/TextBlockView.vue";
import VideoBlockView from "@/components/CardSideView/VideoBlockView.vue";
import EmbedBlockView from "@/components/CardSideView/EmbedBlockView.vue";
import AudioBlockView from "@/components/CardSideView/AudioBlockView.vue";
import UnknownBlockView from "@/components/CardSideView/UnknownBlockView.vue";
import SimpleAudioPlayer from "@/components/SimpleAudioPlayer.vue";

const props = defineProps<{
  blocks: T.ContentBlock[];
  label: T.CardSideName;
}>();
</script>
<style scoped></style>
