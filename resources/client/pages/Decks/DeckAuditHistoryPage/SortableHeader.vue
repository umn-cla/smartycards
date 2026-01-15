<template>
  <button
    type="button"
    class="flex items-center gap-1 hover:text-brand-maroon-900 transition-colors"
    @click="$emit('sort', field)"
  >
    <span>{{ label }}</span>
    <span class="flex flex-col text-brand-maroon-900">
      <CaretUpIcon
        class="size-4"
        :class="isActive && currentDirection === 'asc' ? 'block' : 'hidden'"
      />
      <CaretDownIcon
        class="size-4"
        :class="isActive && currentDirection === 'desc' ? 'block' : 'hidden'"
      />
      <CaretSortIcon
        class="size-4 text-brand-maroon-900/30"
        :class="!isActive ? 'block' : 'hidden'"
      />
    </span>
  </button>
</template>

<script setup lang="ts">
import { computed } from "vue";
import {
  ChevronUpIcon,
  ChevronDownIcon,
  CaretSortIcon,
  CaretDownIcon,
  CaretUpIcon,
} from "@radix-icons/vue";

const props = defineProps<{
  label: string;
  field: string;
  currentSort: string;
  currentDirection: "asc" | "desc";
}>();

defineEmits<{
  sort: [field: string];
}>();

const isActive = computed(() => props.currentSort === props.field);
</script>
