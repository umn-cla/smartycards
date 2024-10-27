<script setup lang="ts">
import { cn } from "@/lib/utils";
import {
  ProgressIndicator,
  ProgressRoot,
  type ProgressRootProps,
} from "radix-vue";
import { computed, type HTMLAttributes } from "vue";

interface ProgressProps extends ProgressRootProps {
  class?: HTMLAttributes["class"];
  variant?: "default" | "primary";
}

const props = withDefaults(defineProps<ProgressProps>(), {
  modelValue: 0,
  default: "default",
});

const delegatedProps = computed(() => {
  const { class: _, ...delegated } = props;

  return delegated;
});
</script>

<template>
  <ProgressRoot
    v-bind="delegatedProps"
    :class="
      cn('relative w-full overflow-hidden rounded-full', props.class, {
        'bg-brand-maroon-900/10 h-2': props.variant === 'default',
        'bg-brand-maroon-900/10 h-4': props.variant === 'primary',
      })
    "
  >
    <ProgressIndicator
      :class="
        cn('h-full w-full flex-1 transition-all', {
          'bg-gradient-to-r from-brand-oatmeal-500 to-brand-maroon-900':
            props.variant === 'default',
          'bg-brand-teal-500': props.variant === 'primary',
        })
      "
      :style="`transform: translateX(-${100 - (props.modelValue ?? 0)}%);`"
    />
  </ProgressRoot>
</template>
