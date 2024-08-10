<template>
  <PageTitle
    as="h3"
    :size="size"
    class="!text-black/25"
  >
    <slot />
  </PageTitle>
</template>
<script setup lang="ts">
import PageTitle from '@/components/PageTitle.vue';

withDefaults(
  defineProps<{
    size?: typeof PageTitle.props.size;
  }>(),
  {
    size: 'default',
  }
);
</script>
<style scoped></style>
