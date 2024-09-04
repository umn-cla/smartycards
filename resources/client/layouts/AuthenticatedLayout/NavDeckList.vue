<template>
  <div
    class="text-xs tracking-wider uppercase leading-6 text-white/25 font-bold"
  >
    {{ label }}
  </div>
  <ul role="list" class="-mx-2 mt-2 space-y-1">
    <li v-for="deck in decks" :key="deck.name">
      <RouterLink
        :to="`/decks/${deck.id}`"
        activeClass="!bg-brand-maroon-950 !text-white"
        :class="[
          'text-white/50  hover:bg-black/25 hover:text-white',
          'group flex gap-x-3 rounded-md p-2 text-sm',
        ]"
      >
        <span class="truncate">{{ deck.name }}</span>
      </RouterLink>
    </li>
  </ul>
</template>
<script setup lang="ts">
import * as T from "@/types";

defineProps<{
  label: string;
  decks: T.Deck[];
}>();
</script>
<style scoped></style>
