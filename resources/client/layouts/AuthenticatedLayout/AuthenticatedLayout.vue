<template>
  <div class="min-h-dvh bg-brand-oatmeal-100">
    <MobileSidebarNav
      v-model:isSidebarOpen="isSidebarOpen"
      :navigation="navigation"
      :myDecks="myDecks"
      :sharedDecks="sharedDecks"
    />

    <DesktopSidebarNav
      v-if="currentUser"
      class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-72 lg:flex-col"
      :navigation="navigation"
      :currentUser="currentUser"
      :myDecks="myDecks"
      :sharedDecks="sharedDecks"
    />

    <AppBar
      v-if="currentUser"
      @update:isSidebarOpen="isSidebarOpen = $event"
      :currentUser="currentUser"
    />

    <main :class="cn('pt-6 sm:py-10 lg:pl-72', containerClass)">
      <div :class="cn('px-4 sm:px-6 lg:px-8')">
        <slot />
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { computed, ref } from "vue";
import { IconDeck, IconGlobe } from "@/components/icons";
import DesktopSidebarNav from "./DesktopSidebarNav.vue";
import MobileSidebarNav from "./MobileSidebarNav.vue";
import AppBar from "./AppBar.vue";
import { useAuthQuery } from "@/queries/auth";
import { useAllDecksQuery } from "@/queries/decks";
import * as T from "@/types";
import { cn } from "@/lib/utils";

defineProps<{
  containerClass?: T.CSSClass;
}>();

const navigation: T.NavMenuItem[] = [
  {
    name: "Decks",
    to: { name: "decks.index" },
    icon: IconDeck,
  },
  {
    name: "Community",
    to: { name: "community.decks.index" },
    icon: IconGlobe,
  },
];

const isSidebarOpen = ref(false);
const { data: currentUser } = useAuthQuery();
const { data: decks } = useAllDecksQuery();

const myDecks = computed((): T.Deck[] => {
  return (
    decks.value?.filter((deck) => deck.current_user_role === "owner") ?? []
  );
});

const sharedDecks = computed((): T.Deck[] => {
  return (
    decks.value?.filter((deck) => deck.current_user_role !== "owner") ?? []
  );
});
</script>
