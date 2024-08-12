<template>
  <button
    class="flex flex-col items-center justify-center rounded-lg text-xs bg-black/5 px-4 py-2 gap-1"
    :class="{
      'text-black': !isHidden,
      'text-black/50': isHidden,
    }"
    @click.stop.prevent="isHidden = !isHidden"
  >
    <div class="flex items-center justify-center gap-2 font-medium">
      {{ meta.label || "Hint" }}
      <Icons.IconEye v-if="!isHidden" class="size-4" />
      <Icons.IconEyeClosed v-else class="size-4" />
    </div>
    <div
      :class="{
        hidden: isHidden,
        block: !isHidden,
      }"
    >
      {{ modelValue || "No hint" }}
    </div>
  </button>
</template>
<script setup lang="ts">
import * as Icons from "@/components/icons";
import { ref } from "vue";

defineProps<{
  modelValue: string; // hidden content
  meta: {
    label: string; // e.g. "Hint"
  };
}>();

const isHidden = ref(true);
</script>
<style scoped></style>
