<template>
  <div>
    <div
      class="flex grow flex-col gap-y-5 overflow-y-auto bg-gradient-to-b from-brand-maroon-800 to-brand-maroon-950 px-6"
    >
      <RouterLink
        :to="{ name: 'decks.index' }"
        class="flex shrink-0 items-center gap-5 py-4 mt-2"
      >
        <BlockMIcon class="h-5 text-brand-gold-500" />
        <SmartycardsWordmark class="text-brand-oatmeal-100 h-5 mt-1" />
      </RouterLink>
      <nav class="flex flex-1 flex-col">
        <ul role="list" class="flex flex-1 flex-col gap-y-7">
          <li>
            <ul role="list" class="-mx-2 space-y-1">
              <li v-for="item in navigation" :key="item.name">
                <RouterLink
                  :to="item.to"
                  activeClass="bg-brand-maroon-950 !text-white"
                  :class="[
                    'text-white/50 hover:bg-brand-maroon-800/25 hover:text-white',
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
            <NavDeckList label="My Decks" :decks="myDecks" />
          </li>
          <li>
            <NavDeckList label="Shared Decks" :decks="sharedDecks" />
          </li>

          <li class="-mx-6 mt-auto">
            <ProfileMenu :currentUser="currentUser" variant="full" />
          </li>
        </ul>
      </nav>
    </div>
  </div>
</template>
<script setup lang="ts">
import * as T from "@/types";
import ProfileMenu from "@/components/ProfileMenu.vue";
import BlockMIcon from "@/components/icons/IconBlockM.vue";
import NavDeckList from "./NavDeckList.vue";
import SmartyFoxHead from "@/assets/smartyfox-head.svg";
import SmartycardsWordmark from "@/components/SmartycardsWordmark.vue";

defineProps<{
  currentUser: T.User;
  navigation: T.NavMenuItem[];
  myDecks: T.Deck[];
  sharedDecks: T.Deck[];
}>();
</script>
<style scoped></style>
