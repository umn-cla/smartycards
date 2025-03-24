<template>
  <SimpleSelect
    :modelValue="modelValue"
    @update:modelValue="$emit('update:modelValue', $event)"
    class="block !w-40"
    placeholder="Select Language (Auto)"
  >
    <SelectOption
      v-for="lang in ttsLanguageOptions"
      :value="lang.locale"
      :key="lang.locale"
    >
      {{ lang.name }}
    </SelectOption>
  </SimpleSelect>
</template>
<script setup lang="ts">
import { ttsLanguageOptions } from "@/lib/ttsLanguageOptions";
import { LanguageOption } from "@/types";
import { SimpleSelect, SelectOption } from "./SimpleSelect";

defineProps<{
  modelValue: LanguageOption["locale"] | null;
}>();

defineEmits<{
  (event: "update:modelValue", value: LanguageOption["locale"] | null): void;
}>();
</script>
<style scoped></style>
