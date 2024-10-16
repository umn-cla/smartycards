<template>
  <math-field
    ref="mathFieldRef"
    class="math-field w-full bg-black/5"
    @input="handleInput"
  >
    {{ modelValue }}
  </math-field>
</template>

<script setup lang="ts">
import { ref } from "vue";
import { MathfieldElement } from "mathlive";

defineProps<{
  modelValue: string;
}>();

const emit = defineEmits<{
  (event: "update:modelValue", value: string): void;
}>();

const mathFieldRef = ref<MathfieldElement | null>(null);

function handleInput(event: Event) {
  console.log("handleInput", {
    value: mathFieldRef.value?.value,
    event: event,
    el: mathFieldRef.value,
  });
  emit("update:modelValue", mathFieldRef.value?.value || "");
}
</script>
<style>
math-field::part(virtual-keyboard-toggle),
math-field::part(menu-toggle) {
  @apply text-brand-maroon-900/50 hover:text-brand-maroon-900;
}
</style>
