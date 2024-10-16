<template>
  <div class="math-block-input">
    <VueMathField
      id="test"
      :modelValue="modelValue"
      @update:modelValue="$emit('update:modelValue', $event)"
    />
    <div class="mt-2">
      <label class="text-xs flex justify-end gap-2">
        Show LaTeX
        <input
          type="checkbox"
          v-model="isShowingLatex"
          class="bg-black/5 border-black/20 rounded-sm"
        />
      </label>
      <label
        class="mt-1 block bg-black/5 rounded-sm focus-within:ring-2 focus-within:ring-offset-1 focus-within:ring-blue-600"
        v-if="isShowingLatex"
      >
        <span class="text-xs text-black/50 px-2">LaTeX Editor</span>
        <code>
          <textarea
            class="block break-all w-full bg-transparent border-none focus:ring-0 px-2 py-0 text-sm"
            :value="modelValue"
            @input="
              $emit(
                'update:modelValue',
                ($event.target as HTMLTextAreaElement).value,
              )
            "
          />
        </code>
      </label>
    </div>
  </div>
</template>
<script setup lang="ts">
import { ref } from "vue";
import VueMathField from "@/components/VueMathField.vue";
import { MathContentBlock } from "../../types";

defineProps<{
  modelValue: MathContentBlock["content"];
}>();

defineEmits<{
  (event: "update:modelValue", value: MathContentBlock["content"]): void;
}>();

const isShowingLatex = ref(false);
</script>
<style scoped></style>
