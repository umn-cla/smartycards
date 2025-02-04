<template>
  <div v-html="html" />
</template>
<script setup lang="ts">
import { computed } from "vue";
import { markdownToHTML } from "@/lib/markdownToHTML";

const props = defineProps<{
  content: string;
}>();

const html = computed(() => markdownToHTML(props.content));
</script>
<style scoped></style>
