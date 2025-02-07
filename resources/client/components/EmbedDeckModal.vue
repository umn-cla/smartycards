<template>
  <Modal
    title="Embed Activity"
    submitButtonLabel="Close"
    triggerButtonVariant="none"
    @submit="$emit('update:isOpen', false)"
    :open="isOpen"
    @update:open="$emit('update:isOpen', $event)"
  >
    <p>Copy the following code to embed this deck on your website.</p>
    <EmbedDeckSection :deck="deck" />
  </Modal>
</template>
<script setup lang="ts">
import * as T from "@/types";
import Modal from "@/components/Modal.vue";
import EmbedDeckSection from "./EmbedDeckSection.vue";

defineProps<{
  deck: T.Deck;
  isOpen: boolean;
}>();

defineEmits<{
  (eventName: "update:isOpen", isOpen: boolean): void;
}>();
</script>
<style scoped></style>
