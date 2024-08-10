<template>
  <div>
    <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-umn-maroon-900 px-6">
      <div class="flex h-16 shrink-0 items-center gap-3">
        <BlockMIcon class="h-5 text-umn-gold-700" />
        <span class="font-semibold text-white">SmartyCards</span>
      </div>
      <nav class="flex flex-1 flex-col">
        <ul
          role="list"
          class="flex flex-1 flex-col gap-y-7"
        >
          <li>
            <ul
              role="list"
              class="-mx-2 space-y-1"
            >
              <li
                v-for="item in navigation"
                :key="item.name"
              >
                <RouterLink
                  :to="item.to"
                  activeClass="bg-umn-maroon-800 !text-white"
                  :class="[
                    'text-white/50 hover:bg-black/50 hover:text-white',
                    'group flex gap-x-3 rounded-md p-2 text-sm font-semibold leading-6',
                  ]"
                >
                  <component
                    :is="item.icon"
                    class="h-6 w-6 shrink-0"
                    aria-hidden="true"
                  />
                  {{ item.name }}
                </RouterLink>
              </li>
            </ul>
          </li>
          <li>
            <NavDeckList
              label="My Decks"
              :decks="myDecks"
            />
          </li>
          <li>
            <NavDeckList
              label="Shared Decks"
              :decks="sharedDecks"
            />
          </li>

          <li class="-mx-6 mt-auto">
            <ProfileMenu
              :currentUser="currentUser"
              variant="full"
            />
          </li>
        </ul>
      </nav>
    </div>
  </div>
</template>
<script setup lang="ts">
import * as T from '@/types';
import ProfileMenu from '@/components/ProfileMenu.vue';
import BlockMIcon from '@/components/icons/IconBlockM.vue';
import NavDeckList from './NavDeckList.vue';

defineProps<{
  currentUser: T.User;
  navigation: T.NavMenuItem[];
  myDecks: T.Deck[];
  sharedDecks: T.Deck[];
}>();
</script>
<style scoped></style>
