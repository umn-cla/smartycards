<script setup lang="ts">
import { cn } from "@/lib/utils";
import {
  ProgressIndicator,
  ProgressRoot,
  type ProgressRootProps,
} from "radix-vue";
import { computed, type HTMLAttributes } from "vue";

const props = withDefaults(
  defineProps<ProgressRootProps & { class?: HTMLAttributes["class"] }>(),
  {
    modelValue: 0,
  },
);

const delegatedProps = computed(() => {
  const { class: _, ...delegated } = props;

  return delegated;
});
</script>

<template>
  <ProgressRoot
    v-bind="delegatedProps"
    :class="
      cn(
        'relative h-2 w-full overflow-hidden rounded-full bg-brand-maroon-900/10 dark:bg-stone-50/5',
        props.class,
      )
    "
  >
    <ProgressIndicator
      class="h-full w-full flex-1 bg-brand-maroon-900/30 transition-all dark:bg-stone-50"
      :style="`transform: translateX(-${100 - (props.modelValue ?? 0)}%);`"
    />
  </ProgressRoot>
</template>
