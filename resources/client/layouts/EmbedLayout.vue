<template>
  <div :class="cn('embed-layout min-h-dvh w-full', props.class)">
    <div :class="cn('p-4', containerClass)">
      <slot />
    </div>
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
