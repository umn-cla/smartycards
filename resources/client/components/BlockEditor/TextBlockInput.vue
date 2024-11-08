<template>
  <div>
    <QuillyEditor
      ref="editor"
      :modelValue="modelValue"
      @update:modelValue="$emit('update:modelValue', $event)"
      :options="options"
      class="bg-brand-maroon-800/5 rounded-sm focus-within:ring-2 focus-within:ring-offset-1 focus-within:ring-blue-600"
    />

    <div class="mt-4 flex gap-4 items-center flex-wrap">
      <div class="flex gap-2 items-center">
        <Label :for="`block-${nonce}__language-select`">Language</Label>
        <Select
          :id="`block-${nonce}__language-select`"
          v-model="selectedLanguage"
        >
          <SelectTrigger class="bg-brand-maroon-800/5">
            <SelectValue placeholder="Language" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem v-for="lang in languages" :value="lang.locale">
              {{ lang.name }}
            </SelectItem>
          </SelectContent>
        </Select>
      </div>

      <button @click="tts.play">
        <IconCirclePlay class="size-6" />
        <span class="sr-only">Play audio</span>
      </button>
    </div>
  </div>
</template>
<script setup lang="ts">
import { QuillyEditor } from "vue-quilly";
import Quill from "quill/quill"; // Core build
import { ref, onMounted, computed, watch } from "vue";
import {
  Select,
  SelectTrigger,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectValue,
} from "@/components/ui/select";
import { Label } from "../ui/label";
import "quill-paste-smart";
import "quill/dist/quill.core.css";
import "quill/dist/quill.bubble.css";
import { getTTSLanguageOptions } from "@/lib/getTtsLanguageOptions";
import { TextContentBlock } from "@/types";
import { uuid } from "@/lib/utils";
import SimpleAudioPlayer from "../SimpleAudioPlayer.vue";
import { useTextToSpeech } from "@/composables/useTextToSpeech";
import IconCirclePlay from "../icons/IconCirclePlay.vue";
import { stripHtml } from "@/lib/stripHtml";

const props = defineProps<{
  modelValue: TextContentBlock["content"];
  meta?: TextContentBlock["meta"];
}>();

const nonce = uuid();

const emit = defineEmits<{
  (event: "update:modelValue", value: string): void;
  (event: "update:meta", value: TextContentBlock["meta"]): void;
}>();

const editor = ref<InstanceType<typeof QuillyEditor>>();
let quill: Quill | null = null;

const selectedLanguage = computed({
  get: () => props.meta?.lang ?? "en-US",
  set: (value) => {
    emit("update:meta", { lang: value });
  },
});

const languages = getTTSLanguageOptions();

const text = computed(() => stripHtml(props.modelValue));
const tts = useTextToSpeech(text, selectedLanguage);

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

onMounted(() => {
  if (!editor.value) {
    throw new Error("Editor element not found");
  }

  quill = editor.value.initialize(Quill);
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
