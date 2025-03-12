<script setup lang="ts">
import { TooltipTrigger, type TooltipTriggerProps } from 'radix-vue'

const props = defineProps<TooltipTriggerProps>()
</script>

<template>
  <TooltipTrigger v-bind="props" type="button">
    <slot />
  </TooltipTrigger>
</template>
