<template>
  <div>
    <Label
      :for="id"
      :class="[{ 'sr-only': labelHidden }, labelClass]"
      :required="required"
    >
      {{ label }}
    </Label>
    <Input
      :id="id"
      :modelValue="modelValue"
      @update:modelValue="$emit('update:modelValue', String($event))"
      :type="type"
      :class="inputClass"
      :placeholder="placeholder"
    />
  </div>
</template>
<script setup lang="ts">
import type { CSSClass } from '@/types';
import { Label } from './ui/label';
import { Input } from './ui/input';

withDefaults(
  defineProps<{
    id: string;
    label: string;
    labelHidden?: boolean;
    modelValue: string;
    type?: string;
    inputClass?: CSSClass;
    labelClass?: CSSClass;
    required?: boolean;
    placeholder?: string;
  }>(),
  {
    labelHidden: false,
    type: 'text',
    required: false,
    inputClass: () => ({}),
    labelClass: () => ({}),
  }
);

defineEmits<{
  (eventName: 'update:modelValue', value: string): void;
}>();
</script>
<style scoped></style>
