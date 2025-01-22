<template>
  <div>
    <Label class="text-sm text-neutral-400">{{ label }}</Label>

    <BlockEditor
      :modelValue="modelValue"
      @update:modelValue="emit('update:modelValue', $event)"
      class="rounded-lg"
      @dragHandle:left="$emit('dragHandle:left', $event)"
      @dragHandle:right="$emit('dragHandle:right', $event)"
      @dragHandle:up="$emit('dragHandle:up', $event)"
      @dragHandle:down="$emit('dragHandle:down', $event)"
    />
  </div>
</template>
<script setup lang="ts">
import * as T from "@/types";
import { BlockEditor } from "@/components/BlockEditor";
import { Label } from "@/components/ui/label";

defineProps<{
  label: string;
  modelValue: T.ContentBlock[];
}>();

const emit = defineEmits<{
  (event: "update:modelValue", value: T.ContentBlock[]): void;
  (event: "dragHandle:left", block: T.ContentBlock): void;
  (event: "dragHandle:right", block: T.ContentBlock): void;
  (event: "dragHandle:up", block: T.ContentBlock): void;
  (event: "dragHandle:down", block: T.ContentBlock): void;
}>();
</script>
<style scoped></style>
