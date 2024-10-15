<template>
  <div class="bg-brand-maroon-800/5 flex flex-col">
    <DragDrop
      :modelValue="modelValue"
      group="block-editor"
      @update:modelValue="emit('update:modelValue', $event)"
    >
      <template #item="{ element: block }">
        <div class="flex border-b border-black/5">
          <button class="cursor-move flex items-start px-1 py-3">
            <Icons.IconDragHandle class="size-4" />
            <span class="sr-only">Drag to reorder</span>
          </button>
          <section class="flex-grow">
            <div class="flex justify-between items-center">
              <h3 class="text-xs text-brand-maroon-800/50 font-base">
                {{ capitalize(block.type) }}
              </h3>
              <button
                class="cursor-pointer flex items-start px-3 py-3"
                @click="removeBlock(block.id)"
              >
                <Icons.IconX class="size-4" />
                <span class="sr-only">Remove block</span>
              </button>
            </div>
            <div class="pr-3 pb-3">
              <component
                :is="getComponentForType(block.type)"
                :modelValue="block.content"
                @update:modelValue="handleUpdateBlockContent(block.id, $event)"
                :meta="block.meta"
                @update:meta="handleUpdateBlockMeta(block.id, $event)"
              />
            </div>
          </section>
        </div>
      </template>
    </DragDrop>
    <DropdownMenu>
      <DropdownMenuTrigger asChild>
        <Button
          variant="ghost"
          class="rounded-t-none hover:bg-brand-maroon-800/10"
        >
          <Icons.IconPlusFilled class="size-4 mr-2" />
          Add Block
        </Button>
      </DropdownMenuTrigger>
      <DropdownMenuContent class="grid grid-cols-2">
        <DropdownMenuItem
          v-for="type in blockTypes"
          :key="type"
          @click="addEditorBlock(type)"
        >
          <component :is="getTypeIcon(type)" class="mr-2" />
          {{ capitalize(type) }}
        </DropdownMenuItem>
      </DropdownMenuContent>
    </DropdownMenu>
  </div>
</template>
<script setup lang="ts">
import { capitalize, type Component, computed } from "vue";
import TextBlockInput from "./TextBlockInput.vue";
import ImageBlockInput from "./ImageBlockInput.vue";
import AudioBlockInput from "./AudioBlockInput.vue";
import EmbedBlockInput from "./EmbedBlockInput.vue";
import VideoBlock from "./VideoBlockInput.vue";
import HintBlock from "./HintBlockInput.vue";
import * as Icons from "../icons";
import DragDrop from "../DragDrop/DragDrop.vue";
import Button from "../ui/button/Button.vue";
import {
  DropdownMenu,
  DropdownMenuTrigger,
  DropdownMenuContent,
  DropdownMenuItem,
} from "../ui/dropdown-menu";
import { type ContentBlock, type ContentBlockType } from "@/types";
import MathBlockInput from "./MathBlockInput.vue";

const lookupComponentType: Record<ContentBlockType, Component> = {
  text: TextBlockInput,
  image: ImageBlockInput,
  audio: AudioBlockInput,
  video: VideoBlock,
  embed: EmbedBlockInput,
  hint: HintBlock,
  math: MathBlockInput,
};

const props = withDefaults(
  defineProps<{
    modelValue?: ContentBlock[];
  }>(),
  {
    modelValue: () => [],
  },
);

const emit = defineEmits<{
  (event: "update:modelValue", value: ContentBlock[]): void;
}>();

const blockTypes = computed(
  () => Object.keys(lookupComponentType) as ContentBlockType[],
);

function addEditorBlock(type: ContentBlock["type"]) {
  const block: ContentBlock = {
    id: crypto.randomUUID(),
    type,
    content: "",
    meta: null,
  };

  if (type === "image") {
    block.meta = { alt: "" };
  }

  if (type === "hint") {
    block.meta = { label: "Hint" };
  }

  emit("update:modelValue", [...props.modelValue, block]);
}

function removeBlock(id: string) {
  emit(
    "update:modelValue",
    props.modelValue.filter((block) => block.id !== id),
  );
}

function getComponentForType(type: ContentBlock["type"]): Component {
  return lookupComponentType[type];
}

function handleUpdateBlockContent(id: string, content: string) {
  emit(
    "update:modelValue",
    props.modelValue.map((block) =>
      block.id === id ? { ...block, content } : block,
    ),
  );
}

function handleUpdateBlockMeta(id: string, meta: Record<string, any>) {
  emit(
    "update:modelValue",
    props.modelValue.map((block) =>
      block.id === id ? { ...block, meta } : block,
    ),
  );
}

function getTypeIcon(type: ContentBlock["type"]): Component {
  switch (type) {
    case "text":
      return Icons.IconTextAa;
    case "image":
      return Icons.IconImage;
    case "audio":
      return Icons.IconWaveForm;
    case "video":
      return Icons.IconVideo;
    case "embed":
      return Icons.IconEmbed;
    case "hint":
      return Icons.IconEyeClosed;
    default:
      return Icons.IconQuestionMark;
  }
}
</script>
<style scoped></style>
