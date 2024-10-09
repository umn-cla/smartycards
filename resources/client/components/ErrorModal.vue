<template>
  <Modal
    variant="danger"
    :title="errorTitle"
    :open="!!error"
    @update:open="errorStore.clearError"
    triggerButtonVariant="none"
    submitButtonLabel="Reload Page"
    cancelButtonLabel="Close"
    @submit="handleRefreshClick"
  >
    {{ message }}
  </Modal>
</template>
<script setup lang="ts">
import { computed } from "vue";
import { ApiError } from "@/api/ApiError";
import Modal from "./Modal.vue";
import { useErrorStore } from "@/stores/useErrorStore";

const errorStore = useErrorStore();

const error = computed(() => errorStore.error);

const errorTitle = computed(() => {
  if (!(error.value instanceof ApiError)) {
    return "Error";
  }

  if (error.value.statusCode === 0) {
    return "Connection Error";
  }

  return `Error: ${error.value.statusCode}`;
});

const message = computed(() => {
  if (typeof error.value === "string") {
    return error.value;
  }

  if (!(error.value instanceof ApiError)) {
    return error.value?.message || "An unknown error occurred.";
  }

  return error.value.message;
});

function handleRefreshClick() {
  errorStore.clearError();
  window.location.reload();
}
</script>
<style scoped></style>
