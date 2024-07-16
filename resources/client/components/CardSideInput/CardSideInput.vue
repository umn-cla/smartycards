<template>
  <section class="flex flex-col gap-4 px-4 py-3 bg-black/5 rounded-lg">
    <SelectCardType
      :id="`${label}-type`"
      :modelValue="modelValue.type"
      @update:modelValue="
        $emit('update:modelValue', {
          ...modelValue,
          type: $event,
        })
      "
    />
    <component
      :id="`${label}-content`"
      :is="InputComponent"
      :modelValue="modelValue.content"
      :deckId="deckId"
      @update:modelValue="
        $emit('update:modelValue', {
          ...modelValue,
          content: $event,
        })
      "
    />

    <InputGroup
      :id="`${label}-hint`"
      label="Hint"
      :modelValue="modelValue.meta.hint ?? ''"
      @update:modelValue="
        $emit('update:modelValue', {
          ...modelValue,
          meta: {
            ...modelValue.meta,
            hint: $event,
          },
        })
      "
      inputClass="bg-black/5"
    />
  </section>
</template>
<script setup lang="ts">
import * as T from '@/types';
import { computed } from 'vue';
import SelectCardType from './SelectCardType.vue';
import CardSideTextInput from './CardSideTextInput.vue';
import CardSideImageInput from './CardSideImageInput.vue';
import CardSideAudioInput from './CardSideAudioInput.vue';
import CardSideEmbedInput from './CardSideEmbedInput.vue';
import InputGroup from '../InputGroup.vue';

const props = defineProps<{
  deckId: number;
  label: string;
  modelValue: T.CardSide;
}>();

defineEmits<{
  (event: 'update:modelValue', value: T.CardSide): void;
}>();

const typeToComponentMap = {
  text: CardSideTextInput,
  image: CardSideImageInput,
  audio: CardSideAudioInput,
  embed: CardSideEmbedInput,
};

const InputComponent = computed(() => typeToComponentMap[props.modelValue.type]);
</script>
<style scoped></style>
