<template>
  <div class="flex gap-1 items-baseline">
    <Label for="starting-side-select" class="sr-only">Start Side</Label>
    <SimpleSelect :modelValue="modelValue" @update:modelValue="handleUpdate" id="starting-side-select">
      <SelectOption value="front">Front</SelectOption>
      <SelectOption value="back">Back</SelectOption>
      <SelectOption value="random">Random</SelectOption>
    </SimpleSelect>
  </div>
</template>
<script setup lang="ts">
import { SimpleSelect, SelectOption } from "@/components/SimpleSelect";
import { Label } from "@/components/ui/label";
import type * as T from "@/types";

defineProps<{
  modelValue: T.CardSideName | "random";
}>();

const emit = defineEmits<{
  "update:modelValue": [value: T.CardSideName | "random"];
}>();

function handleUpdate(value: string) {
  emit("update:modelValue", value as T.CardSideName | "random");
}
</script>
