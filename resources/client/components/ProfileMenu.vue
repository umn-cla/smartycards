<template>
  <DropdownMenu>
    <DropdownMenuTrigger asChild>
      <Button
        variant="secondary"
        :class="{
          'h-8 w-8 rounded-full': variant === 'avatar-only',
          'flex w-full justify-start p-4 rounded-none': variant === 'full',
        }"
        class="relative text-brand-oatmeal-50 flex items-center gap-4"
      >
        <Avatar class="h-8 w-8 bg-brand-oatmeal-50 text-brand-maroon-800">
          <AvatarFallback>
            {{ initials }}
          </AvatarFallback>
        </Avatar>
        <template v-if="variant === 'full'">
          <span>{{ currentUser.name }}</span>
        </template>
      </Button>
    </DropdownMenuTrigger>
    <DropdownMenuContent class="w-56" align="end">
      <DropdownMenuLabel class="font-normal flex">
        <div class="flex flex-col space-y-1">
          <p class="text-sm font-medium leading-none">
            {{ currentUser.name }}
          </p>
          <p class="text-xs leading-none text-muted-foreground">
            {{ currentUser.email }}
          </p>
        </div>
      </DropdownMenuLabel>
      <DropdownMenuSeparator />
      <DropdownMenuItem asChild>
        <RouterLink :to="{ name: 'profile' }">
          <IconUser class="size-5 mr-2" />
          Profile
        </RouterLink>
      </DropdownMenuItem>
      <DropdownMenuItem
        asChild
        v-if="currentUser.capabilities?.canViewAdminPages"
      >
        <a href="/admin"><IconAdminPanel class="size-5 mr-2" /> Admin </a>
      </DropdownMenuItem>
      <DropdownMenuItem asChild>
        <a :href="config.api.logoutUrl">
          <IconLogOut class="size-5 mr-2" />
          Log out</a
        >
      </DropdownMenuItem>
    </DropdownMenuContent>
  </DropdownMenu>
</template>
<script setup lang="ts">
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar";
import { Button } from "@/components/ui/button";
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuGroup,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuShortcut,
  DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";
import { computed } from "vue";
import type { User } from "@/types";
import config from "@/config";
import { IconAdminPanel, IconExit, IconUser } from "./icons";
import IconLogOut from "./icons/IconLogOut.vue";

const props = withDefaults(
  defineProps<{
    currentUser: User;
    variant?: "full" | "avatar-only";
  }>(),
  {
    variant: "full",
  },
);

const initials = computed(() =>
  props.currentUser.name
    .split(" ")
    .map((n) => n[0])
    .join("")
    .slice(0, 2),
);
</script>
