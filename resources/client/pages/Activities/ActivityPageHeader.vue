<template>
  <header class="flex flex-col mb-8 rounded-xl mx-auto max-w-screen-sm gap-6">
    <div
      class="flex gap-4 flex-wrap items-center"
      :class="{
        'justify-center': !subtitle,
        'justify-between': subtitle || $slots.actions,
      }"
    >
      <div>
        <Button asChild v-if="backTo" variant="secondary">
          <RouterLink :to="backTo">
            <Icon name="arrow-left" />
            <span class="sr-only">Back</span>
          </RouterLink>
        </Button>
        <h1 class="font-bold text-brand-maroon-800 text-lg sm:text-xl">
          {{ title }}
        </h1>
      </div>
      <p
        class="font-bold text-lg text-brand-maroon-800/50 text-center"
        v-if="subtitle"
      >
        {{ subtitle }}
      </p>
      <slot name="actions" />
    </div>
    <slot />
  </header>
</template>
<script setup lang="ts">
import { RouteLocationRaw } from "vue-router";

defineProps<{
  title?: string;
  subtitle?: string;
  backTo?: RouteLocationRaw;
}>();
</script>
<style scoped></style>
