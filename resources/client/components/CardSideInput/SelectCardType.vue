<template>
  <div>
    <Label :for="id">Card Type</Label>
    <Select
      :id="id"
      :modelValue="modelValue"
      @update:modelValue="(val: string) => $emit('update:modelValue', val as T.CardSide['type'])"
    >
      <SelectTrigger class="bg-black/5">
        <SelectValue placeholder="Select a card type" />
      </SelectTrigger>
      <SelectContent>
        <SelectGroup>
          <SelectItem value="text">Text</SelectItem>
          <SelectItem value="image">Image</SelectItem>
          <SelectItem value="audio">Audio</SelectItem>
          <SelectItem value="embed">Embed (video)</SelectItem>
        </SelectGroup>
      </SelectContent>
    </Select>
  </div>
</template>
<script setup lang="ts">
import * as T from '@/types';
import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select';
import { Label } from '@/components/ui/label';

defineProps<{
  id: string;
  modelValue: T.CardSide['type'];
}>();

defineEmits<{
  (event: 'update:modelValue', value: T.CardSide['type']): void;
}>();
</script>
<style scoped></style>
