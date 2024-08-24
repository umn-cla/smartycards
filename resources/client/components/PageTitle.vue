<template>
  <component
    :is="as"
    class="font-bold text-brand-maroon-950"
    :class="{
      'text-5xl lg:text-6xl': size === 'lg',
      'text-4xl lg:text-5xl': size === 'default',
      'text-3xl lg:text-4xl': size === 'sm',
      'text-2xl lg:text-3xl': size === 'xs',
    }"
  >
    <slot />
  </component>
</template>
<script setup lang="ts">
withDefaults(
  defineProps<{
    size?: "lg" | "default" | "sm" | "xs";
    as?: string;
  }>(),
  {
    size: "default",
    as: "h2",
  },
);
</script>
<style scoped></style>
