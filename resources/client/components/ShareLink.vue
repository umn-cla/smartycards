<template>
  <div class="flex gap-2 items-baseline flex-wrap">
    <Label
      for="share-view-link"
      class="text-brand-maroon-800 flex gap-1 mt-4 mb-2 w-10"
    >
      {{ capitalize(type) }}
    </Label>
    <CopyableInput :value="url" id="share-view-link" class="flex-1">
      <Modal
        title="Regenerate View Link?"
        submitButtonLabel="Regenerate"
        @submit="$emit('regenerate')"
      >
        <template #trigger>
          <Button variant="ghost" class="flex gap-2">
            <IconRefresh />
            <span class="sr-only">Regenerate</span>
          </Button>
        </template>

        <p class="mt-4">
          Are you sure you want to regenerate the <b>{{ type }}</b> link?
        </p>
      </Modal>
    </CopyableInput>
  </div>
</template>
<script setup lang="ts">
import Modal from "./Modal.vue";
import CopyableInput from "./CopyableInput.vue";
import { Label } from "./ui/label";
import { Button } from "./ui/button";
import { IconRefresh } from "./icons";
import { capitalize } from "vue";

defineProps<{
  type: "view" | "edit";
  url: string;
}>();

defineEmits<{
  (event: "regenerate"): void;
}>();
</script>
<style scoped></style>
