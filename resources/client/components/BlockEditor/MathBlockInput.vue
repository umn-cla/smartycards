<template>
  <div class="">
    <VueMathField
      id="test"
      :modelValue="modelValue"
      @update:modelValue="$emit('update:modelValue', $event)"
    />
    <code>{{ modelValue }}</code>
  </div>
</template>
<script setup lang="ts">
import VueMathField from "@/components/VueMathField.vue";
import { MathContentBlock } from "../../types";

defineProps<{
  modelValue: MathContentBlock["content"];
}>();

defineEmits<{
  (event: "update:modelValue", value: MathContentBlock["content"]): void;
}>();
</script>
<style scoped></style>
