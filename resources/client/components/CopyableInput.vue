<template>
  <div class="flex gap-2">
    <Input
      :id="id"
      type="text"
      readonly="readonly"
      :modelValue="value"
      class="font-mono text-sm bg-brand-maroon-800/5"
    />
    <Button
      :icon="copied ? 'check' : 'content_copy'"
      @click="copy()"
      class="flex gap-2"
      variant="outline"
    >
      <IconCopy v-if="!copied" />
      <IconCheck v-else />
      <span class="sr-only">{{ copied ? "Copied" : "Copy" }}</span>
    </Button>
    <slot />
  </div>
</template>
<script setup lang="ts">
import { toRef } from "vue";
import { useClipboard } from "@vueuse/core";
import { IconCopy, IconCheck } from "./icons";
import { Input } from "./ui/input";
import { Button } from "./ui/button";

const props = defineProps<{
  id: string;
  value: string;
}>();

const valueRef = toRef(props, "value");

const { copy, copied } = useClipboard({
  source: valueRef,
});
</script>
<style scoped></style>
