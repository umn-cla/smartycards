<template>
  <div class="mb-8 border-b border-gray-200 pb-4">
    <nav
      class="mb-4"
      v-if="backTo"
    >
      <RouterLink
        :to="backTo"
        class="flex gap-2 items-center"
      >
        <IconChevronLeft class="size-5" />
        {{ backLabel }}
      </RouterLink>
    </nav>

    <header
      class="flex gap-8 flex-wrap justify-between items-center"
      :class="headerClass"
    >
      <div
        :class="{
          'flex gap-2 items-baseline': size === 'xs',
        }"
      >
        <PageTitle :size="size">{{ title }}</PageTitle>
        <PageSubtitle
          v-if="subtitle"
          :size="size"
          class="mt-1"
          >{{ subtitle }}</PageSubtitle
        >
      </div>
      <slot />
    </header>
  </div>
</template>
<script setup lang="ts">
import PageTitle from '@/components/PageTitle.vue';
import PageSubtitle from '@/components/PageSubtitle.vue';
import IconChevronLeft from '@/components/icons/IconChevronLeft.vue';
import { type RouteLocationRaw } from 'vue-router';
import type { CSSClass } from '@/types';

withDefaults(
  defineProps<{
    title: string;
    subtitle?: string;
    backLabel?: string;
    backTo?: RouteLocationRaw | null;
    headerClass?: CSSClass;
    size?: 'lg' | 'default' | 'sm' | 'xs';
  }>(),
  {
    backLabel: 'Back',
    backTo: null,
    subtitle: '',
    headerClass: '',
    size: 'default',
  }
);
</script>
<style scoped></style>
