<template>
  <AudioPlayer :src="block.content" class="flex items-center justify-center" />
</template>
<script setup lang="ts">
import { AudioContentBlock } from "@/types";
import AudioPlayer from "@/components/AudioPlayer.vue";

defineProps<{
  block: AudioContentBlock;
}>();
</script>
<style scoped></style>
