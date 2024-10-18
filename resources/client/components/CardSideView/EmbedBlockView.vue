<template>
  <EmbedVideo :src="block.content" />
</template>
<script setup lang="ts">
import { EmbedContentBlock } from "@/types";
import EmbedVideo from "../EmbedVideo.vue";

defineProps<{
  block: EmbedContentBlock;
}>();
</script>
<style scoped></style>
