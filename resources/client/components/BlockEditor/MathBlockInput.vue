<template>
  <div class="math-block-input">
    <label :for="makeInputId('math-field')" class="sr-only"> Math Field</label>
    <VueMathField
      :id="makeInputId('math-field')"
      :modelValue="modelValue"
      @update:modelValue="$emit('update:modelValue', $event)"
    />
    <div class="mt-2">
      <label
        class="text-xs flex justify-end gap-2"
        :for="makeInputId('show-latex')"
      >
        Show LaTeX
        <input
          :id="makeInputId('show-latex')"
          type="checkbox"
          v-model="isShowingLatex"
          class="bg-black/5 border-black/20 rounded-sm"
        />
      </label>
      <label
        :for="makeInputId('latex-editor')"
        class="mt-1 block bg-black/5 rounded-sm focus-within:ring-2 focus-within:ring-offset-1 focus-within:ring-blue-600"
        v-if="isShowingLatex"
      >
        <span class="text-xs text-brand-maroon-900/50 px-2">LaTeX Editor</span>
        <code>
          <textarea
            :id="makeInputId('latex-editor')"
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
import { MathContentBlock } from "@/types";
import { useMakeInputId } from "@/composables/useMakeInputId";

const props = defineProps<{
  id: MathContentBlock["id"];
  modelValue: MathContentBlock["content"];
}>();

defineEmits<{
  (event: "update:modelValue", value: MathContentBlock["content"]): void;
}>();

const { makeInputId } = useMakeInputId("math-block-input", props.id);
const isShowingLatex = ref(false);
</script>
<style scoped></style>
