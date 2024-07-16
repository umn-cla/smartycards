<template>
  <HoverCard :openDelay="200">
    <HoverCardTrigger class="inline-flex">
      <IconQuestionMark />
      <span class="sr-only">More Info</span>
    </HoverCardTrigger>
    <HoverCardContent>
      <slot />
    </HoverCardContent>
  </HoverCard>
</template>
<script setup lang="ts">
import IconQuestionMark from './icons/IconQuestionMark.vue';
import { HoverCard, HoverCardTrigger } from './ui/hover-card';
import HoverCardContent from './ui/hover-card/HoverCardContent.vue';
</script>
<style scoped></style>
