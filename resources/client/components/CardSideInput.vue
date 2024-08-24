<template>
  <div>
    <Label class="text-sm text-neutral-400">{{ label }}</Label>

    <BlockEditor
      :modelValue="modelValue"
      @update:modelValue="emit('update:modelValue', $event)"
      class="rounded-lg overflow-clip"
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
}>();
</script>
<style scoped></style>
