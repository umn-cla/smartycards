<template>
  <button
    type="button"
    class="flex items-center gap-1 hover:text-brand-maroon-900 transition-colors"
    @click="$emit('sort', field)"
  >
    <span>{{ label }}</span>
    <span class="flex flex-col">
      <ChevronUpIcon
        class="size-3 -mb-1"
        :class="isActive && currentDirection === 'asc'
          ? 'text-brand-teal-600'
          : 'text-brand-maroon-900/30'"
      />
      <ChevronDownIcon
        class="size-3"
        :class="isActive && currentDirection === 'desc'
          ? 'text-brand-teal-600'
          : 'text-brand-maroon-900/30'"
      />
    </span>
  </button>
</template>

<script setup lang="ts">
import { computed } from "vue";
import { ChevronUpIcon, ChevronDownIcon } from "@radix-icons/vue";

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
