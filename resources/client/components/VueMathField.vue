<template>
  <math-field
    ref="mathFieldRef"
    class="math-field text-brand-maroon-900"
    :class="{
      'bg-black/5 w-full': !readOnly,
      'bg-transparent w-auto': readOnly,
    }"
    :readOnly="readOnly"
    @input="handleInput"
  >
    {{ modelValue }}
  </math-field>
</template>

<script setup lang="ts">
import { ref } from "vue";
import { MathfieldElement } from "mathlive";

withDefaults(
  defineProps<{
    modelValue: string;
    readOnly?: boolean;
  }>(),
  {
    readOnly: false,
  },
);

const emit = defineEmits<{
  (event: "update:modelValue", value: string): void;
}>();

const mathFieldRef = ref<MathfieldElement | null>(null);

function handleInput(event: Event) {
  emit("update:modelValue", mathFieldRef.value?.value || "");
}
</script>
<style>
math-field::part(virtual-keyboard-toggle),
math-field::part(menu-toggle) {
  @apply text-brand-maroon-900/50 hover:text-brand-maroon-900;
}
</style>
