<template>
  <Draggable
    :modelValue="modelValue"
    @update:modelValue="$emit('update:modelValue', $event)"
    :componentData="{
      tag: 'ul',
      type: 'transition-group',
      name: !isDragging ? 'duration-500' : null,
    }"
    :group="group"
    :disabled="disabled"
    :animation="200"
    ghostClass="ghost"
    @start="isDragging = true"
    @end="isDragging = false"
    itemKey="id"
    class="dragdrop__container"
  >
    <template #item="{ element }">
      <li class="drag-drop__list-item list-none">
        <slot
          name="item"
          :element="element"
        />
      </li>
    </template>
  </Draggable>
</template>
<script setup lang="ts" generic="T extends { id: string | number }">
import { ref } from 'vue';
import Draggable from 'vuedraggable';

const props = withDefaults(
  defineProps<{
    modelValue: T[];
    group: string;
    disabled?: boolean;
  }>(),
  {
    disabled: false,
  }
);

defineEmits<{
  (eventName: 'update:modelValue', value: T[]): void;
}>();

const isDragging = ref(false);
</script>
<style>
.dragdrop__container .ghost {
  opacity: 0.5;
  background: #c8ebfb;
}
</style>
