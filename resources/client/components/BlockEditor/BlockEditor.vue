<template>
  <div class="bg-brand-maroon-800/5 flex flex-col">
    <DragDrop
      :modelValue="modelValue"
      group="block-editor"
      @update:modelValue="emit('update:modelValue', $event)"
      handle=".drag-handle"
    >
      <template #item="{ element: block }">
        <div
          class="flex border-b border-black/5"
          data-cy="content-block-container"
          :id="`block-editor__block__${block.id}`"
        >
          <button
            class="drag-handle cursor-move flex items-start px-1 py-3 focus:ring-2 focus:ring-blue-600"
            @click="($event.target as HTMLButtonElement).focus()"
            @keydown.up="moveBlock(block, 'up')"
            @keydown.down="moveBlock(block, 'down')"
          >
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
                data-cy="remove-content-block-button"
              >
                <Icons.IconX class="size-4" />
                <span class="sr-only">Remove block</span>
              </button>
            </div>
            <div class="pr-3 pb-3">
              <component
                :id="block.id"
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
import { capitalize, type Component, computed, nextTick } from "vue";
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
import { makeContentBlock } from "@/lib/makeContentBlock";
import { clamp, move } from "ramda";
import invariant from "tiny-invariant";

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

const blockTypes = computed(() => {
  const types = Object.keys(lookupComponentType) as ContentBlockType[];
  return types.toSorted();
});

function addEditorBlock(type: ContentBlock["type"]) {
  emit("update:modelValue", [...props.modelValue, makeContentBlock(type)]);
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

function handleUpdateBlockMeta(id: string, meta: Record<string, unknown>) {
  emit(
    "update:modelValue",
    props.modelValue.map((block) =>
      block.id === id ? { ...block, meta } : block,
    ),
  );
}

function getTypeIcon(type: ContentBlock["type"]): Component {
  const icons = {
    text: Icons.IconTextAa,
    image: Icons.IconImage,
    audio: Icons.IconWaveForm,
    video: Icons.IconVideo,
    embed: Icons.IconEmbed,
    hint: Icons.IconEyeClosed,
    math: Icons.IconMath,
  } as Record<ContentBlockType, Component>;

  return icons[type] ?? Icons.IconQuestionMark;
}

const focusBlockHandle = (block: ContentBlock) => {
  const el = document.querySelector<HTMLButtonElement>(
    `#block-editor__block__${block.id} .drag-handle`,
  );
  el?.focus();
};

function moveBlock(block: ContentBlock, direction: "up" | "down") {
  const delta = direction === "up" ? -1 : 1;

  const fromIndex = props.modelValue.findIndex((b) => b.id === block.id);
  invariant(
    fromIndex >= 0,
    `Index of block with id ${block.id} not found in modelValue`,
  );
  const toIndex = clamp(0, props.modelValue.length - 1, fromIndex + delta);

  const updatedArray = move(fromIndex, toIndex, props.modelValue);

  emit("update:modelValue", updatedArray);

  // return focus after dom updates
  nextTick(() => focusBlockHandle(block));
}
</script>
<style scoped></style>
