<template>
  <div :class="cn('embed-layout min-h-dvh w-full', props.class)">
    <main
      id="main-content"
      tabindex="-1"
      :class="cn('p-4 focus:outline-none', containerClass)"
    >
      <slot />
    </main>
  </div>
</template>

<script setup lang="ts">
import { cn } from "@/lib/utils";
import * as T from "@/types";

const props = defineProps<{
  class?: T.CSSClass;
  containerClass?: T.CSSClass;
}>();
</script>
