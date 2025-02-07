<template>
  <div class="flex gap-2 items-baseline flex-wrap">
    <Label
      for="share-view-link"
      class="text-brand-maroon-800 flex gap-1 mt-4 mb-2 w-16"
    >
      {{ capitalize(type) }}
    </Label>
    <CopyableInput :value="url" id="share-view-link" class="flex-1">
      <Modal
        title="Reset View Link?"
        submitButtonVariant="destructive"
        submitButtonLabel="Reset View Link"
        @submit="$emit('regenerate')"
      >
        <template #trigger>
          <Button variant="ghost" class="flex gap-2">
            <IconRefresh />
            <span class="sr-only">Reset</span>
          </Button>
        </template>

        <div class="flex flex-col gap-4 mt-4">
          <p>
            Resetting the {{ type }} link is a good option if you don't want new
            users to have {{ type }} access to this deck. Current users will
            retain access, but the
            <b>previous {{ type }} link</b>
            <template v-if="type === 'view'">
              and <b>any embedded views</b>
            </template>
            will <b>no longer work</b>.
          </p>

          <p>
            Once this {{ type }} link reset is done, you may remove old members
            with {{ type }} permissions if you wish.
          </p>

          <p><b>Warning:</b> This action cannot be undone.</p>
        </div>
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
