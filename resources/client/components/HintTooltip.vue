<template>
  <TooltipProvider>
    <Tooltip :delayDuration="0">
      <TooltipTrigger
        class="bg-brand-maroon-800/70 hover:bg-brand-maroon-800 rounded-full size-4 text-brand-oatmeal-100 text-xs inline-flex items-center justify-center"
      >
        ?
      </TooltipTrigger>
      <TooltipContent class="w-60 py-2">
        <slot />
      </TooltipContent>
    </Tooltip>
  </TooltipProvider>
</template>
<script setup lang="ts">
import IconQuestionMark from "./icons/IconQuestionMark.vue";
import {
  TooltipProvider,
  Tooltip,
  TooltipContent,
  TooltipTrigger,
} from "./ui/tooltip";
</script>
<style scoped></style>
