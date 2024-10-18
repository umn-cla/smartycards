<template>
  <div class="">
    <template v-for="block in blocks" :key="block.id">
      <TextBlockView
        v-if="isTextBlock(block)"
        :block="block"
        :class="{
          'flex items-center justify-center': blocks.length === 1,
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
          'flex-1 flex items-center justify-center': blocks.length === 1,
        }"
      />
      <HintBlockView
        v-else-if="isHintBlock(block)"
        :modelValue="block.content"
        :meta="block.meta"
      />
      <MathBlockView v-else-if="isMathBlock(block)" :block="block" />
      <div v-else>
        <p>Unknown block type: {{ block.type }}</p>
        <pre>{{ block }}</pre>
      </div>
    </template>
  </div>
</template>
<script setup lang="ts">
import * as T from "@/types";
import { computed } from "vue";
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
import EmbedVideo from "@/components/EmbedVideo.vue";
import AudioPlayer from "@/components/AudioPlayer.vue";

const props = defineProps<{
  side: T.CardSide;
}>();

const blocks = computed(() => props.side);
</script>
<style scoped></style>
