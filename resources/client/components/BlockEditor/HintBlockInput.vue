<template>
  <div data-cy="hint-block-input">
    <Label :for="makeInputId('hint-text')" class="sr-only">Hint Text</Label>
    <InputGroup
      :id="makeInputId('hint-text')"
      label="Hidden Text"
      :labelHidden="true"
      :modelValue="modelValue"
      @update:modelValue="$emit('update:modelValue', $event)"
      placeholder="Hint text"
    />
  </div>
</template>
<script setup lang="ts">
import { Label } from "@/components/ui/label";
import InputGroup from "@/components/InputGroup.vue";
import { useMakeInputId } from "@/composables/useMakeInputId";
import { ContentBlock } from "@/types";

const props = defineProps<{
  id: ContentBlock["id"];
  modelValue: string; // hidden content
  meta: {
    label: string; // e.g. "Hint"
  };
}>();

defineEmits<{
  (event: "update:modelValue", value: string): void;
  (event: "update:meta", value: { label: string }): void;
}>();

const { makeInputId } = useMakeInputId("hint-block-input", props.id);
</script>
<style scoped></style>
