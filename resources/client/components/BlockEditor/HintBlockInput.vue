<template>
  <div :id="`hint-block-${id}`">
    <Label :for="`hint-block-${id}__text`" class="sr-only">Hint</Label>
    <InputGroup
      :id="`hint-block-${id}__text`"
      label="Hidden Text"
      :labelHidden="true"
      :modelValue="modelValue"
      @update:modelValue="$emit('update:modelValue', $event)"
      placeholder="Hidden content"
    />
  </div>
</template>
<script setup lang="ts">
import { Label } from "@/components/ui/label";
import InputGroup from "@/components/InputGroup.vue";

const id = crypto.randomUUID();

defineProps<{
  modelValue: string; // hidden content
  meta: {
    label: string; // e.g. "Hint"
  };
}>();

defineEmits<{
  (event: "update:modelValue", value: string): void;
  (event: "update:meta", value: { label: string }): void;
}>();
</script>
<style scoped></style>
