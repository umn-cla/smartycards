<template>
  <div class="relative" data-cy="text-block-input-container">
    <SimpleTTSPlayer
      :text="text"
      :selectedLanguage="selectedLanguage"
      class="top-1 right-1 absolute z-10"
      isIdleClass="bg-brand-oatmeal-50"
      v-if="isDeckTTSEnabled && charCount < MAX_TTS_CHARS"
    />

    <label :for="makeInputId('text-block')" class="sr-only">Text Block</label>
    <QuillyEditor
      ref="editor"
      :modelValue="modelValue"
      @update:modelValue="$emit('update:modelValue', $event)"
      :options="options"
      class="bg-brand-maroon-800/5 rounded-sm focus-within:ring-2 focus-within:ring-offset-1 focus-within:ring-blue-600"
      data-cy="text-block-input"
    />

    <div
      class="flex gap-2 items-center justify-end mt-2"
      v-if="isDeckTTSEnabled"
    >
      <label :for="makeInputId('language-select')" class="sr-only">
        Text-to-Speech Language
      </label>
      <Select
        v-if="isSettingCustomLanguage"
        :id="makeInputId('language-select')"
        :modelValue="selectedLanguage"
        @update:modelValue="
          $emit('update:meta', { ...meta, lang: $event ?? null })
        "
      >
        <SelectTrigger class="bg-brand-maroon-800/5">
          <SelectValue placeholder="Language (Auto)"> </SelectValue>
        </SelectTrigger>
        <SelectContent>
          <SelectItem
            v-for="lang in languages"
            :value="lang.locale"
            :key="lang.locale"
          >
            {{ lang.name }}
          </SelectItem>
        </SelectContent>
      </Select>
      <Toggle
        :modelValue="isSettingCustomLanguage"
        label="Set Language"
        @update:modelValue="
          () => {
            isSettingCustomLanguage = !isSettingCustomLanguage;
            if (!isSettingCustomLanguage) {
              $emit('update:meta', { ...meta, lang: null });
            }
          }
        "
      >
        <IconGlobe class="size-5" />
      </Toggle>
    </div>
  </div>
</template>
<script setup lang="ts">
import { QuillyEditor } from "vue-quilly";
import Quill from "quill/quill"; // Core build
import { ref, onMounted, computed, watch, inject, toRef } from "vue";
import {
  Select,
  SelectTrigger,
  SelectContent,
  SelectItem,
  SelectValue,
} from "@/components/ui/select";
import "quill-paste-smart";
import "quill/dist/quill.core.css";
import "quill/dist/quill.bubble.css";
import { getTTSLanguageOptions } from "@/lib/getTtsLanguageOptions";
import { TextContentBlock } from "@/types";
import { IconGlobe } from "../icons";
import Toggle from "@/components/Toggle.vue";
import { IS_DECK_TTS_ENABLED_INJECTION_KEY, MAX_TTS_CHARS } from "@/constants";
import { useMakeInputId } from "@/composables/useMakeInputId";
import SimpleTTSPlayer from "@/components/SimpleTTSPlayer.vue";

const props = defineProps<{
  id: TextContentBlock["id"];
  modelValue: TextContentBlock["content"];
  meta?: TextContentBlock["meta"];
}>();

defineEmits<{
  (event: "update:modelValue", value: string): void;
  (event: "update:meta", value: TextContentBlock["meta"]): void;
}>();

const editor = ref<InstanceType<typeof QuillyEditor>>();
let quill: Quill | null = null;

const { makeInputId } = useMakeInputId("text-block-input", props.id);

const selectedLanguage = computed((): string => props.meta?.lang ?? "");
const isSettingCustomLanguage = ref(!!selectedLanguage.value);
const languages = getTTSLanguageOptions();
const text = computed(() => props.modelValue);
const charCount = computed(() => text.value.length);

const options = computed(() => ({
  theme: "bubble",
  // bounds: editor.value ? editor.value.$el : null,
  modules: {
    toolbar: [
      [
        "bold",
        "italic",
        { list: "ordered" },
        { list: "bullet" },
        "link",
        "code-block",
        "formula",
      ],
      ["clean"],
    ],
    keyboard: {
      bindings: {
        // disable tab key
        tab: {
          key: "Tab",
          handler: () => true,
        },
        clearFormatting: {
          key: "\\",
          shortKey: true,
          handler(range, context) {
            if (!quill) {
              return;
            }
            quill.removeFormat(range.index, range.length, Quill.sources.USER);
          },
        },
      },
    },
  },
  placeholder: "Write something...",
  readOnly: false,
}));

const isDeckTTSEnabled = inject(
  IS_DECK_TTS_ENABLED_INJECTION_KEY,
  toRef(false),
);

onMounted(() => {
  if (!editor.value) {
    throw new Error("Editor element not found");
  }

  quill = editor.value.initialize(Quill);

  // some attrs to help with accessibility of the contenteditable area
  const contentEditableAttrs = {
    id: makeInputId("text-block"),
    role: "textbox",
    "aria-multiline": "true",
  };

  Object.entries(contentEditableAttrs).forEach(([key, value]) => {
    if (!quill?.root || !quill.root.contentEditable) {
      console.error("Can't set attrs for Quilly Editor. Quill root not found.");
      return;
    }

    quill.root.setAttribute(key, value);
  });
});
</script>
<style scoped></style>
<style type="postcss">
.ql-editor {
  /* prevent clipping link tooltips in bubble theme */
  overflow: visible;
  @apply font-serif text-base leading-5;
}
</style>
