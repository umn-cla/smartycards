<template>
  <Select
    :id="id"
    :modelValue="modelValue ?? ''"
    class="block !w-40"
    @update:modelValue="
      (locale: LanguageOption['locale'] | '') =>
        $emit('update:modelValue', locale || null)
    "
  >
    <SelectOption
      v-for="lang in ttsLanguageOptions"
      :value="lang.locale"
      :key="lang.locale"
    >
      {{ lang.name }}
    </SelectOption>
  </Select>
</template>
<script setup lang="ts">
import Select from "./SimpleSelect/Select.vue";
import { ttsLanguageOptions } from "@/lib/ttsLanguageOptions";
import { LanguageOption } from "@/types";
import { SelectOption } from "./SimpleSelect";

defineProps<{
  id: string;
  modelValue: LanguageOption["locale"] | null;
}>();

defineEmits<{
  (event: "update:modelValue", value: LanguageOption["locale"] | null): void;
}>();
</script>
<style scoped></style>
