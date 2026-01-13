<template>
  <div :class="cn('min-h-dvh w-full', props.class)">
    <div :class="cn('pt-6 sm:py-10', containerClass)">
      <div :class="cn('px-4 sm:px-6')">
        <slot />
      </div>
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
