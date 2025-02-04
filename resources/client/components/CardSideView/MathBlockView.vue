<template>
  <div class="math-block-view flex flex-wrap">
    <VueMathField
      :readOnly="true"
      :modelValue="block.content"
      :class="{
        'text-lg': block.content.length >= 20,
        'text-xl': block.content.length < 20,
        'text-2xl': block.content.length < 5,
      }"
    />
  </div>
</template>
<script setup lang="ts">
import * as T from "@/types";
import VueMathField from "../VueMathField.vue";

defineProps<{
  block: T.MathContentBlock;
}>();
</script>
<style scoped></style>
