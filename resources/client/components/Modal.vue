<template>
  <Dialog>
    <DialogTrigger asChild v-if="triggerButtonVariant !== 'none'">
      <slot name="trigger">
        <Button :variant="triggerButtonVariant">{{
          triggerButtonLabel || title
        }}</Button>
      </slot>
    </DialogTrigger>
    <DialogContent class="sm:max-w-[425px]">
      <DialogHeader>
        <div
          :class="{
            'sm:flex sm:gap-4': variant === 'danger',
          }"
        >
          <IconExclamationTriangle
            class="size-12 sm:size-10 text-red-500 bg-red-50 p-2 rounded-full justify-self-center mx-auto"
            v-if="variant === 'danger'"
          />
          <div
            :class="{
              'flex-grow mt-5 sm:mt-3': variant === 'danger',
            }"
          >
            <DialogTitle>{{ title }}</DialogTitle>
            <DialogDescription class="my-2">
              <slot>
                <p v-if="description">{{ description }}</p>
              </slot>
            </DialogDescription>
          </div>
        </div>
      </DialogHeader>

      <DialogFooter v-if="hasFooter && !noFooter">
        <slot name="footer">
          <DialogClose asChild v-if="cancelButtonVariant !== 'none'">
            <Button :variant="cancelButtonVariant" @click="$emit('cancel')"
              >{{ cancelButtonLabel }}
            </Button>
          </DialogClose>
          <DialogClose asChild v-if="submitButtonVariant !== 'none'">
            <Button
              type="submit"
              :variant="submitButtonVariant"
              :disabled="submitButtonDisabled"
              @click="$emit('submit')"
              >{{ submitButtonLabel }}</Button
            >
          </DialogClose>
        </slot>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>

<script setup lang="ts">
import { Button } from "@/components/ui/button";
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
  DialogClose,
} from "@/components/ui/dialog";
import { IconExclamationTriangle } from "@/components/icons";
import { computed } from "vue";

type ModalButtonVariant =
  | "default"
  | "outline"
  | "secondary"
  | "destructive"
  | "ghost"
  | "none";

const props = withDefaults(
  defineProps<{
    title: string;
    description?: string;
    triggerButtonLabel?: string;
    triggerButtonVariant?: ModalButtonVariant;
    cancelButtonLabel?: string;
    cancelButtonVariant?: ModalButtonVariant;
    submitButtonLabel?: string;
    submitButtonVariant?: ModalButtonVariant;
    submitButtonDisabled?: boolean;
    variant?: "default" | "danger";
    noFooter?: boolean;
  }>(),
  {
    variant: "default",
    triggerButtonLabel: "", // fallback to title
    triggerButtonVariant: "default",
    cancelButtonLabel: "Cancel",
    cancelButtonVariant: "ghost",
    submitButtonLabel: "Submit",
    submitButtonDisabled: false,
    submitButtonVariant: "default",
    noFooter: false,
  },
);

defineEmits<{
  (eventName: "submit"): void;
  (eventName: "cancel"): void;
}>();

const hasFooter = computed(() => {
  return (
    !props.noFooter ||
    props.cancelButtonVariant !== "none" ||
    props.submitButtonVariant !== "none"
  );
});
</script>
