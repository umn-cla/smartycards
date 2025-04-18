<template>
  <div class="relative" data-cy="text-block-input-container">
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
      class="flex gap-2 items-center justify-end mt-2 text-xs flex-wrap"
      v-if="isTTSEnabled"
    >
      <label :for="makeInputId('language-select')" class="sr-only">
        Language
      </label>
      <SelectLanguage
        v-if="isSettingCustomLanguage"
        :id="makeInputId('language-select')"
        :modelValue="selectedLanguage"
        @update:modelValue="
          $emit('update:meta', { ...meta, lang: $event || null })
        "
      />
      <Toggle
        :modelValue="isSettingCustomLanguage"
        label="Set Language"
        data-cy="set-language-toggle"
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
      <SimpleTTSPlayer
        :text="text"
        :selectedLanguage="ttsLanguage"
        isIdleClass="bg-brand-oatmeal-50 !text-brand-maroon-800/75"
        v-if="isTTSEnabled && charCount < MAX_TTS_CHARS"
        class="float-right m-1 rounded-sm relative z-10"
      />
    </div>
  </div>
</template>
<script setup lang="ts">
import { QuillyEditor } from "vue-quilly";
import Quill from "quill/quill";
import { ref, onMounted, computed, watch } from "vue";
import SelectLanguage from "../SelectLanguage.vue";
import "quill-paste-smart";
import "quill/dist/quill.core.css";
import "quill/dist/quill.snow.css";
import { TextContentBlock } from "@/types";
import { MAX_TTS_CHARS } from "@/constants";
import { useMakeInputId } from "@/composables/useMakeInputId";
import SimpleTTSPlayer from "@/components/SimpleTTSPlayer.vue";
import { useTTSContext } from "@/composables/useTTSContext";
import { IconGlobe } from "../icons";
import Toggle from "@/components/Toggle.vue";

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

const selectedLanguage = ref(props.meta?.lang ?? "auto");

const text = computed(() => props.modelValue);
const charCount = computed(() => text.value.length);
const ttsLanguage = computed(() => {
  // use the selected language if it's set, otherwise use the default language
  return selectedLanguage.value || defaultLanguageOption.value.locale || "auto";
});

const options = computed(() => ({
  theme: "snow",
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
        { direction: "rtl" }, // text direction
        "clean",
      ],
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

const { isTTSEnabled, defaultLanguageOption } = useTTSContext();

const isCustomLang = (locale: string) =>
  !!locale && // must be defined
  locale !== defaultLanguageOption.value.locale; // and not the default

const isSettingCustomLanguage = ref(isCustomLang(selectedLanguage.value));

watch(
  () => props.meta?.lang,
  (lang) => {
    selectedLanguage.value =
      lang || defaultLanguageOption.value.locale || "auto";

    isSettingCustomLanguage.value = isCustomLang(selectedLanguage.value);
  },
  { immediate: true },
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

.ql-toolbar.ql-snow {
  border: none;
}
.ql-container.ql-snow {
  border: none;
}

.ql-toolbar.ql-snow button {
  @apply text-brand-maroon-800 opacity-50;
}

.ql-toolbar.ql-snow :is(button:hover, button:focus) {
  @apply text-brand-maroon-800 opacity-100 bg-brand-maroon-800/5 rounded;
}

.ql-toolbar.ql-snow :is(button.ql-active) {
  @apply text-brand-oatmeal-50 opacity-100 bg-brand-maroon-800 rounded;
}

.ql-toolbar.ql-snow :is(.ql-stroke, button:hover .ql-stroke) {
  @apply stroke-current;
}

.ql-toolbar.ql-snow
  :is(
    .ql-fill,
    .ql-stroke.ql-fill,
    button:hover .ql-fill,
    button:hover .ql-stroke.ql-fill
  ) {
  @apply fill-current;
}
</style>
