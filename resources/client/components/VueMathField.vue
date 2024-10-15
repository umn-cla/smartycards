<template>
  <math-field ref="mathFieldRef" class="w-full" @input="handleInput">
    {{ localValue }}
  </math-field>
</template>

<script setup lang="ts">
import { nextTick, onMounted, ref } from "vue";
import { MathfieldElement } from "mathlive";

const props = defineProps<{
  modelValue: string;
}>();

const localValue = ref(props.modelValue);

const mathFieldRef = ref<MathfieldElement | null>(null);

function handleInput(event: Event) {
  console.log("handleInput", {
    value: mathFieldRef.value?.getAttribute("value"),
    event: event,
    el: mathFieldRef.value,
  });
}

onMounted(() => {
  if (!mathFieldRef.value) {
    throw new Error("Mathfield element not found");
  }
  mathFieldRef.value.addEventListener("input", (e) => {
    console.log("addedEventListerer trigger");
    handleInput(e);
  });
});

// function handleKeypress(event: Event) {
//   if (!mathFieldRef.value) return;
//   console.log({
//     value: mathFieldRef.value.value,
//     event: event,
//     el: mathFieldRef.value,
//   });
//   nextTick(() => {
//     if (!mathFieldRef.value) return;
//     localValue.value = mathFieldRef.value.value;
//     console.log(localValue.value);
//   });
// }

// onMounted(() => {
//   if (!mathFieldRef.value) {
//     throw new Error("Mathfield element not found");
//   }

//   // mathFieldRef.value.addEventListener("input", handleInput);
//   mathFieldRef.value.addEventListener("keypress", handleKeypress);
// });
</script>
