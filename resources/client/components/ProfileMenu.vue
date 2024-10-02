<template>
  <DropdownMenu>
    <DropdownMenuTrigger asChild>
      <Button
        variant="secondary"
        :class="{
          'h-8 w-8 rounded-full': variant === 'avatar-only',
          'flex w-full justify-start p-4': variant === 'full',
        }"
        class="relative text-white flex items-center gap-4"
      >
        <Avatar class="h-8 w-8">
          <AvatarFallback> {{ initials }} </AvatarFallback>
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
      <DropdownMenuGroup>
        <DropdownMenuItem asChild>
          <RouterLink :to="{ name: 'profile' }"> Profile </RouterLink>
        </DropdownMenuItem>
        <DropdownMenuItem disabled> Settings </DropdownMenuItem>
      </DropdownMenuGroup>
      <DropdownMenuSeparator />
      <DropdownMenuItem asChild>
        <a :href="config.api.logoutUrl">Log out</a>
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
