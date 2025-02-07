<template>
  <div class="min-h-dvh bg-brand-oatmeal-100 min-w-fit">
    <main :class="cn('pt-6 sm:py-10', containerClass)">
      <div :class="cn('px-4 sm:px-6')">
        <slot />
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import * as T from "@/types";
import { cn } from "@/lib/utils";

defineProps<{
  containerClass?: T.CSSClass;
}>();
</script>
