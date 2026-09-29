<template>
  <div>
    <BackLink v-if="backTo" :to="backTo">{{ backLabel }}</BackLink>

    <header
      class="flex gap-8 flex-wrap justify-between items-center"
      :class="headerClass"
    >
      <div
        :class="{
          'flex gap-2 items-baseline': size === 'xs',
        }"
      >
        <div class="flex gap-2 items-center flex-wrap">
          <PageTitle :size="size">{{ title }} </PageTitle>
          <slot name="title-append" />
        </div>

        <PageSubtitle v-if="subtitle" :size="size" class="mt-1">{{
          subtitle
        }}</PageSubtitle>
      </div>
      <slot />
    </header>
  </div>
</template>
<script setup lang="ts">
import PageTitle from "@/components/PageTitle.vue";
import PageSubtitle from "@/components/PageSubtitle.vue";
import BackLink from "@/components/BackLink.vue";
import { type RouteLocationRaw } from "vue-router";
import type { CSSClass } from "@/types";
import Badge from "./ui/badge/Badge.vue";

withDefaults(
  defineProps<{
    title: string;
    subtitle?: string;
    backLabel?: string;
    backTo?: RouteLocationRaw | null;
    headerClass?: CSSClass;
    size?: "lg" | "default" | "sm" | "xs";
  }>(),
  {
    backLabel: "Back",
    backTo: null,
    subtitle: "",
    headerClass: "",
    size: "default",
  },
);
</script>
<style scoped></style>
