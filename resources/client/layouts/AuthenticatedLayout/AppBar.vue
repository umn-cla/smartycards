<template>
  <div
    class="sticky top-0 z-40 flex items-center justify-between bg-brand-maroon-800 px-4 py-4 shadow-sm sm:px-6 lg:hidden gap-2"
  >
    <div class="flex items-center gap-2">
      <button
        type="button"
        class="text-brand-oatmeal-300 hover:text-brand-oatmeal-50 lg:hidden hover:bg-white/10 p-2 rounded-md -ml-2"
        @click="$emit('update:isSidebarOpen', true)"
      >
        <span class="sr-only">Open sidebar</span>
        <IconMenu class="h-6 w-6" aria-hidden="true" />
      </button>
      <RouterLink :to="{ name: 'decks.index' }">
        <SmartycardsWordmark class="text-brand-gold-500 h-6 mt-1" />
      </RouterLink>
    </div>
    <ProfileMenu :currentUser="currentUser" variant="avatar-only" />
  </div>
</template>
<script setup lang="ts">
import * as T from "@/types";
import { IconBlockM, IconMenu } from "@/components/icons";
import ProfileMenu from "@/components/ProfileMenu.vue";
import SmartycardsWordmark from "@/components/SmartycardsWordmark.vue";

defineProps<{
  currentUser: T.User;
}>();

defineEmits<{
  (eventName: "update:isSidebarOpen", value: boolean): void;
}>();
</script>
<style scoped></style>
