<template>
  <Select
    :id="id"
    :modelValue="modelValue ?? ''"
    @update:modelValue="
      (locale: LanguageOption['locale'] | '') =>
        $emit('update:modelValue', locale || null)
    "
  >
    <SelectTrigger
      class="bg-brand-maroon-800/5"
      data-cy="select-language__trigger"
    >
      <SelectValue placeholder="Language (Auto)"></SelectValue>
    </SelectTrigger>
    <SelectContent data-cy="select-language__content">
      <SelectItem
        v-for="lang in ttsLanguageOptions"
        :value="lang.locale"
        :key="lang.locale"
      >
        {{ lang.name }}
      </SelectItem>
    </SelectContent>
  </Select>
</template>
<script setup lang="ts">
import {
  Select,
  SelectTrigger,
  SelectContent,
  SelectItem,
  SelectValue,
} from "@/components/ui/select";
import { ttsLanguageOptions } from "@/lib/ttsLanguageOptions";
import { LanguageOption } from "@/types";

defineProps<{
  id: string;
  modelValue: LanguageOption["locale"] | null;
}>();

defineEmits<{
  (event: "update:modelValue", value: LanguageOption["locale"] | null): void;
}>();
</script>
<style scoped></style>
