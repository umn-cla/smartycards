<template>
  <div
    class="sticky top-0 z-40 flex items-center gap-x-6 bg-umn-maroon-900 px-4 py-4 shadow-sm sm:px-6 lg:hidden"
  >
    <button
      type="button"
      class="-m-2.5 p-2.5 text-neutral-200 lg:hidden"
      @click="$emit('update:isSidebarOpen', true)"
    >
      <span class="sr-only">Open sidebar</span>
      <IconMenu
        class="h-6 w-6"
        aria-hidden="true"
      />
    </button>
    <RouterLink
      class="flex-1 text-sm font-semibold leading-6 text-white flex items-center gap-3"
      :to="{ name: 'decks.index' }"
    >
      <IconBlockM class="h-4 text-umn-gold-500" />
      SmartyCards
    </RouterLink>
    <ProfileMenu
      :currentUser="currentUser"
      variant="avatar-only"
    />
  </div>
</template>
<script setup lang="ts">
import * as T from '@/types';
import { IconBlockM, IconMenu } from '@/components/icons';
import ProfileMenu from '@/components/ProfileMenu.vue';

defineProps<{
  currentUser: T.User;
}>();

defineEmits<{
  (eventName: 'update:isSidebarOpen', value: boolean): void;
}>();
</script>
<style scoped></style>
