<template>
  <div class="mb-4">
    <RouterLink :to="to" class="flex gap-2 items-center">
      <IconChevronLeft class="size-5" />
      <slot />
    </RouterLink>
  </div>
</template>
<script setup lang="ts">
import { type RouteLocationRaw } from "vue-router";
import IconChevronLeft from "@/components/icons/IconChevronLeft.vue";

defineProps<{
  to: RouteLocationRaw;
}>();
</script>
