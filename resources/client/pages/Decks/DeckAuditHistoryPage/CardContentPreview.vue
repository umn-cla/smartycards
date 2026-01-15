<template>
  <div class="space-y-2">
    <div v-if="blocks.length === 0" class="text-brand-maroon-900/50 italic">
      (empty)
    </div>
    <div
      v-for="(block, index) in blocks"
      :key="block.id ?? index"
      class="flex items-start gap-2"
    >
      <span
        class="inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-medium rounded bg-brand-maroon-900/10 text-brand-maroon-900/70"
      >
        {{ formatBlockType(block.type) }}
      </span>
      <span class="flex-1 break-words">{{ getBlockPreview(block) }}</span>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { ContentBlock } from "@/types";

defineProps<{
  blocks: ContentBlock[];
}>();

function formatBlockType(type: string): string {
  return type.charAt(0).toUpperCase() + type.slice(1);
}

function getBlockPreview(block: ContentBlock): string {
  switch (block.type) {
    case "text":
      return stripHtml(String(block.content || ""));
    case "image":
      return block.meta?.alt ? `Image: ${block.meta.alt}` : "Image";
    case "audio":
      return "Audio file";
    case "video":
      return "Video";
    case "embed":
      return `Embed: ${block.content}`;
    case "hint":
      return `Hint: ${block.content}`;
    case "math":
      return `Math: ${block.content}`;
    default:
      return JSON.stringify(block.content);
  }
}

function stripHtml(html: string): string {
  const tmp = document.createElement("div");
  tmp.innerHTML = html;
  return tmp.textContent || tmp.innerText || "";
}
</script>
