<template>
  <div class="p-2">
    <div v-if="isValidUrlComputed && modelValue">
      <iframe
        :src="modelValue"
        class="w-full h-full"
        frameborder="0"
        allowfullscreen
        referrerpolicy="strict-origin-when-cross-origin"
      ></iframe>
    </div>
    <div
      v-else
      class="aspect-video bg-white/50 rounded-lg overflow-clip flex items-center justify-center text-neutral-400"
    >
      Invalid URL
    </div>
    <InputGroup
      :id="`embedUrl`"
      label="Embed Url"
      :labelHidden="true"
      required
      :modelValue="modelValue"
      @update:modelValue="emit('update:modelValue', $event)"
      placeholder="Embed Url"
      inputClass="bg-black/5"
      class="mt-4"
    />
  </div>
</template>
<script setup lang="ts">
import { computed } from 'vue';
import { isValidUrl } from '@/lib/utils';
import InputGroup from '@/components/InputGroup.vue';
import EmbedVideo from '@/components/EmbedVideo.vue';

const props = defineProps<{
  modelValue: string;
}>();

const emit = defineEmits<{
  (event: 'update:modelValue', value: string): void;
}>();

const isValidUrlComputed = computed(() => isValidUrl(props.modelValue));
</script>
<style scoped></style>
