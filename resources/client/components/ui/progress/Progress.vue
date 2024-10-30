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
  variant?: "default" | "secondary";
}

const props = withDefaults(defineProps<ProgressProps>(), {
  modelValue: 0,
  variant: "default",
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
        'bg-brand-maroon-900/10 h-1': props.variant === 'secondary',
      })
    "
  >
    <ProgressIndicator
      :class="
        cn('h-full w-full flex-1 transition-all', {
          'bg-gradient-to-r from-brand-oatmeal-500 to-brand-maroon-800':
            props.variant === 'default',
          'bg-gradient-to-r from-brand-gold-shadow to-brand-teal-300':
            props.variant === 'secondary',
        })
      "
      :style="`transform: translateX(-${100 - (props.modelValue ?? 0)}%);`"
    />
  </ProgressRoot>
</template>
