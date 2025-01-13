<template>
  <div>
    <EmbedVideo
      v-if="isValidUrlComputed"
      :src="modelValue"
      class="aspect-video rounded-lg overflow-clip"
    />
    <div
      v-else
      class="aspect-video bg-white/50 rounded-lg overflow-clip flex items-center justify-center text-neutral-400"
    >
      Invalid URL
    </div>
    <InputGroup
      :id="makeInputId('video-block')"
      label="YouTube"
      :labelHidden="true"
      required
      :modelValue="modelValue"
      @update:modelValue="emit('update:modelValue', $event)"
      placeholder="https://www.youtube.com/embed/VIDEO_ID"
      inputClass="bg-brand-maroon-800/5"
      class="mt-4"
    />
  </div>
</template>
<script setup lang="ts">
import { computed } from "vue";
import { isValidUrl } from "@/lib/utils";
import InputGroup from "@/components/InputGroup.vue";
import EmbedVideo from "@/components/EmbedVideo.vue";
import { useMakeInputId } from "@/composables/useMakeInputId";

const props = defineProps<{
  modelValue: string;
}>();

const emit = defineEmits<{
  (event: "update:modelValue", value: string): void;
}>();

const isValidUrlComputed = computed(() => isValidUrl(props.modelValue));
const { makeInputId } = useMakeInputId("video-block-input");
</script>
<style scoped></style>
