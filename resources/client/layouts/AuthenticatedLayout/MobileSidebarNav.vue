<template>
  <TransitionRoot as="template" :show="isSidebarOpen">
    <Dialog
      class="relative z-50 lg:hidden"
      @close="$emit('update:isSidebarOpen', false)"
    >
      <TransitionChild
        as="template"
        enter="transition-opacity ease-linear duration-300"
        enterFrom="opacity-0"
        enterTo="opacity-100"
        leave="transition-opacity ease-linear duration-300"
        leaveFrom="opacity-100"
        leaveTo="opacity-0"
      >
        <div class="fixed inset-0 bg-black/80" />
      </TransitionChild>

      <div class="fixed inset-0 flex">
        <TransitionChild
          as="template"
          enter="transition ease-in-out duration-300 transform"
          enterFrom="-translate-x-full"
          enterTo="translate-x-0"
          leave="transition ease-in-out duration-300 transform"
          leaveFrom="translate-x-0"
          leaveTo="-translate-x-full"
        >
          <DialogPanel class="relative mr-16 flex w-full max-w-xs flex-1">
            <TransitionChild
              as="template"
              enter="ease-in-out duration-300"
              enterFrom="opacity-0"
              enterTo="opacity-100"
              leave="ease-in-out duration-300"
              leaveFrom="opacity-100"
              leaveTo="opacity-0"
            >
              <div
                class="absolute left-full top-0 flex w-16 justify-center pt-5"
              >
                <button
                  type="button"
                  class="-m-2.5 p-2.5"
                  @click="$emit('update:isSidebarOpen', false)"
                >
                  <span class="sr-only">Close sidebar</span>
                  <IconX class="h-6 w-6 text-white" aria-hidden="true" />
                </button>
              </div>
            </TransitionChild>
            <!-- Sidebar component -->
            <div
              class="flex grow flex-col gap-y-5 overflow-y-auto bg-gradient-to-b from-brand-maroon-800 to-brand-maroon-950 px-6 pb-2 ring-1 ring-white/10"
            >
              <div
                class="flex h-16 shrink-0 items-center justify-center border-b border-white/50"
              >
                <IconBlockM
                  class="h-4 w-auto text-brand-gold-500"
                  alt="University of Minnesota"
                />
              </div>
              <nav class="flex flex-1 flex-col">
                <ul role="list" class="flex flex-1 flex-col gap-y-7">
                  <li>
                    <ul role="list" class="-mx-2 space-y-1">
                      <li v-for="item in navigation" :key="item.name">
                        <RouterLink
                          :to="item.to"
                          activeClass="!bg-brand-maroon-950 !text-white"
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
                </ul>
              </nav>
            </div>
          </DialogPanel>
        </TransitionChild>
      </div>
    </Dialog>
  </TransitionRoot>
</template>
<script setup lang="ts">
import {
  Dialog,
  DialogPanel,
  TransitionChild,
  TransitionRoot,
} from "@headlessui/vue";
import { IconBlockM, IconX } from "@/components/icons";
import type { Deck, NavMenuItem } from "@/types";
import NavDeckList from "./NavDeckList.vue";

const isSidebarOpen = defineModel<boolean>("isSidebarOpen", { required: true });

defineProps<{
  navigation: NavMenuItem[];
  myDecks: Deck[];
  sharedDecks: Deck[];
}>();
</script>
<style scoped></style>
