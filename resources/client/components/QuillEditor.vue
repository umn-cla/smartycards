<template>
  <div ref="editorContainerRef" class="quill-editor"></div>
</template>
<script setup lang="ts">
import { onMounted, ref, useId, watch } from "vue";
import Quill, { QuillOptions, Range } from "quill";
import { Delta } from "quill/core";
import Emitter from "quill/core/emitter";
import { mergeDeepRight } from "ramda";
import axios from "@/api/axios";
import QuillBetterImage from "@umn-latis/quill-better-image-module";
import "quill-paste-smart";
import "quill/dist/quill.core.css";
import "quill/dist/quill.snow.css";

const props = withDefaults(
  defineProps<{
    id?: string;
    modelValue: string;
    imageUploadUrl?: string | null;
    options?: Partial<QuillOptions>;
  }>(),
  {
    imageUploadUrl: null,
    options: () => ({}),
  },
);

const emit = defineEmits<{
  (eventName: "update:modelValue", value: string): void;
}>();

const editorContainerRef = ref<HTMLElement | null>(null);
const editorHtml = ref("");

// using a ref causes errors
// @see: https://github.com/slab/quill/issues/4293
let quill: Quill | null = null;

const defaultOptions = {
  theme: "snow",
  placeholder: "Write something...",
  readOnly: false,
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
    // see: https://github.com/Artem-Schander/quill-paste-smart
    clipboard: {
      allowed: {
        tags: [
          "a",
          "b",
          "strong",
          "u",
          "s",
          "i",
          "p",
          "br",
          "ul",
          "ol",
          "li",
          "span",
        ],
        attributes: ["href", "rel", "target", "class"],
      },
      magicPasteLinks: true,
    },
    uploader: {
      mimetypes: [
        "image/png",
        "image/jpeg",
        "image/jpg",
        "image/webp",
        "image/svg+xml",
        "image/gif",
      ],
      async handler(range: Range, files: File[]) {
        if (!props.imageUploadUrl) {
          throw new Error("Image upload URL is not set");
        }

        if (!quill) {
          throw new Error("Quill editor is not set");
        }

        const form = new FormData();
        form.append("file", files[0]);

        const res = await axios.post(props.imageUploadUrl, form);

        const imageUrl = res.data.url as string;

        const imageDelta = new Delta()
          .retain(range.index) // Retain the text before the range
          .delete(range.length) // Delete the selected text
          .insert({ image: imageUrl }); // Insert the image

        // apply the new delta to the editor
        quill.updateContents(imageDelta, Emitter.sources.USER);

        // move the cursor to the end of the image
        quill.setSelection(
          range.index + imageDelta.length(),
          Emitter.sources.SILENT,
        );
      },
    },
    betterImage: {},
  },
};

function registerQuillModules() {
  // suppress warning
  Quill.register(`modules/betterImage`, QuillBetterImage, true);
}

onMounted(() => {
  if (!editorContainerRef.value) {
    throw new Error("Editor container ref is not set");
  }

  const mergedOptions = mergeDeepRight(defaultOptions, props.options);

  // todo: make better image module self-registering
  registerQuillModules();

  quill = new Quill(editorContainerRef.value, mergedOptions);

  // some attrs to help with accessibility of the contenteditable area
  const contentEditableAttrs = {
    id: props.id || useId(),
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

  // set the initial value
  if (props.modelValue) {
    pasteHTML(props.modelValue, quill);
  }

  quill.on("text-change", () => {
    // HACK: remove style attribute from images
    // this is a workaround to make sure image resize styles
    // aren't emitted in the html – which causes them to show up
    // when rendered outside the editor.
    // There's probably a better solution but this works for now.
    const images = quill?.root.querySelectorAll("img");
    images?.forEach((img) => {
      img.removeAttribute("style");
    });

    editorHtml.value = quill?.root.innerHTML ?? "";
    emit("update:modelValue", editorHtml.value);
  });
});

// Convert modelValue HTML to Delta and replace editor content
const pasteHTML = (content: string, quillInstance: Quill) => {
  const delta = quillInstance.clipboard.convert({ html: content ?? "" });
  quillInstance.setContents(delta);
  return delta;
};

// Watch modelValue and update the editor content
// if the modelValue is changed from outside
watch(
  () => props.modelValue,
  (newValue) => {
    if (!quill) {
      return;
    }

    // if the value is the same as the editorHtml
    if (newValue === editorHtml.value) {
      return;
    }

    // if changed update the editorHtml
    editorHtml.value = newValue;

    if (!newValue) {
      quill.setContents([]);
      return;
    }

    pasteHTML(newValue, quill);
  },
);
</script>
<style>
.ql-editor {
  min-height: 10rem;
}

.ql-container {
  font-size: 1rem;
}
</style>
