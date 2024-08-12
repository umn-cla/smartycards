<template>
  <div
    class="p-4 flex flex-col items-center justify-center rounded-lg mb-4 w-full"
  >
    <Button
      variant="secondary"
      @click.stop.prevent="isHidden = !isHidden"
      class="flex items-center gap-2 w-full my-0"
      :class="{
        'rounded-b-none shadow-none border-b-white/50 border-b': !isHidden,
      }"
    >
      {{ meta.label || "Hint" }}
      <Icons.IconEye v-if="!isHidden" class="size-4" />
      <Icons.IconEyeClosed v-else class="size-4" />
    </Button>
    <div
      v-if="!isHidden"
      class="text-sm w-full px-4 py-2 text-center bg-black/5 rounded-md rounded-t-none"
    >
      {{ modelValue || "No hint" }}
    </div>
  </div>
</template>
<script setup lang="ts">
import * as Icons from "@/components/icons";
import { Button } from "@/components/ui/button";
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
