<script setup lang="ts">
import { type HTMLAttributes, computed } from 'vue'
import { TabsList, type TabsListProps } from 'radix-vue'
import { cn } from '@/lib/utils'

const props = defineProps<TabsListProps & { class?: HTMLAttributes['class'] }>()

const delegatedProps = computed(() => {
  const { class: _, ...delegated } = props

  return delegated
})
</script>

<template>
  <TabsList
    v-bind="delegatedProps"
    :class="cn(
      'inline-flex h-9 items-center justify-center rounded-lg bg-stone-100 p-1 text-stone-500 dark:bg-stone-800 dark:text-stone-400',
      props.class,
    )"
  >
    <slot />
  </TabsList>
</template>
