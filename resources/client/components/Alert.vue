<template>
  <div
    class="rounded-md outline p-4"
    :class="{
      'bg-yellow-50 outline-yellow-400/50': type === 'warning',
      'bg-blue-50 outline-blue-400/50': type === 'info',
      'bg-green-50 outline-green-400/50': type === 'success',
      'bg-red-50 outline-red-400/50': type === 'error',
    }"
  >
    <div class="flex">
      <div class="shrink-0">
        <IconComponent
          class="size-5"
          :class="{
            'text-yellow-500': type === 'warning',
            'text-blue-500': type === 'info',
            'text-green-500': type === 'success',
            'text-red-500': type === 'error',
          }"
          aria-hidden="true"
        />
      </div>
      <div
        class="ml-3 text-sm flex flex-col gap-2"
        :class="{
          'text-yellow-700': type === 'warning',
          'text-blue-700': type === 'info',
          'text-green-700': type === 'success',
          'text-red-700': type === 'error',
        }"
      >
        <h3 class="font-bold" v-if="title">
          {{ title }}
        </h3>
        <p v-if="message">
          {{ message }}
        </p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from "vue";
import {
  ExclamationTriangleIcon,
  CheckCircledIcon,
  InfoCircledIcon,
  CrossCircledIcon,
} from "@radix-icons/vue";

const props = defineProps<{
  type: "info" | "warning" | "error" | "success";
  title?: string;
  message?: string;
}>();

const iconMap = {
  info: InfoCircledIcon,
  warning: ExclamationTriangleIcon,
  error: CrossCircledIcon,
  success: CheckCircledIcon,
};
const IconComponent = computed(() => {
  return iconMap[props.type];
});
</script>
