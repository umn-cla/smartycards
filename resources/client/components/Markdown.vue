<template>
  <div v-html="html" />
</template>
<script setup lang="ts">
import { computed } from "vue";
import { marked } from "marked";
import markedKatex from "marked-katex-extension";

const props = defineProps<{
  content: string;
}>();

const options = {
  throwOnError: false,
};

marked.use(markedKatex(options));

const html = computed(() => {
  const html = marked.parse(props.content);
  return html;
});
</script>
<style scoped></style>
