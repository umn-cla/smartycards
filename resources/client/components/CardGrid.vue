<template>
  <section class="my-8 card-grid-container">
    <h3 v-if="title" class="text-3xl font-bold text-neutral-900">
      {{ title }}
    </h3>
    <ul
      v-if="items.length || $slots.prepend || $slots.append"
      class="card-grid my-4"
    >
      <li v-if="$slots.prepend" class="prepend-item">
        <slot name="prepend" />
      </li>
      <li v-for="item in items" :key="item[key]">
        <slot :item="item" />
      </li>
      <li v-if="$slots.append" class="append-item">
        <slot name="append" />
      </li>
    </ul>
    <p v-else-if="fallback" class="my-4">{{ fallback }}</p>
  </section>
</template>
<script setup lang="ts" generic="T">
import { CSSClass } from "@/types";

withDefaults(
  defineProps<{
    title?: string;
    items: T[];
    // key in T generic
    key?: string;
    fallback?: string;
  }>(),
  {
    title: "",
    key: "id",
    fallback: "No items found",
  },
);
</script>
<style scoped></style>
