<template>
  <div :id="`reveal-block-${id}`">
    <div class="p-4 flex flex-col gap-2 items-center justify-center rounded-lg bg-white/50 mb-4">
      <Button
        variant="secondary"
        @click="isHidden = !isHidden"
        class="flex items-center gap-2"
      >
        {{ meta.label || 'Hint' }}
        <Icons.IconEye
          v-if="!isHidden"
          class="size-4"
        />
        <Icons.IconEyeClosed
          v-else
          class="size-4"
        />
      </Button>
      <div
        v-if="!isHidden"
        class="text-sm"
      >
        {{ modelValue || 'Hidden content' }}
      </div>
    </div>
    <div class="grid grid-cols-2 gap-2">
      <InputGroup
        :id="`reveal-block-${id}__label`"
        label="Label"
        :labelHidden="true"
        :modelValue="meta.label"
        @update:modelValue="$emit('update:meta', { label: $event })"
        placeholder="Button Label"
      />
      <InputGroup
        :id="`reveal-block-${id}__text`"
        label="Hidden Text"
        :labelHidden="true"
        :modelValue="modelValue"
        @update:modelValue="$emit('update:modelValue', $event)"
        placeholder="Hidden content"
      />
    </div>
  </div>
</template>
<script setup lang="ts">
import * as Icons from '@/components/icons';
import { ref } from 'vue';
import InputGroup from '@/components/InputGroup.vue';
import { Button } from '@/components/ui/button';

const id = crypto.randomUUID();

defineProps<{
  modelValue: string; // hidden content
  meta: {
    label: string; // e.g. "Hint"
  };
}>();

defineEmits<{
  (event: 'update:modelValue', value: string): void;
  (event: 'update:meta', value: { label: string }): void;
}>();

const isHidden = ref(true);
</script>
<style scoped></style>
