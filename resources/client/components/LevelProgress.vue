<template>
  <div class="flex gap-4 items-center">
    <span
      class="flex-shrink-0 text-brand-maroon-900/50 uppercase text-xs font-bold"
    >
      Level {{ currentLevel }}
    </span>
    <Progress :modelValue="percentToNextLevel" variant="default" />
    <span class="flex-shrink-0 text-xs text-brand-maroon-900/50">
      {{ xpEarnedForCurrentLevel }} / {{ xpNeeded }} XP
    </span>
  </div>
</template>
<script setup lang="ts">
import { Badge } from "@/components/ui/badge";
import { Progress } from "@/components/ui/progress";
import { computed } from "vue";
import {
  getLevelFromTotalXP,
  getXPEarnedAtThisLevel,
  getXPNeededAtThisLevel,
} from "@/lib/getXPLevel";

const props = defineProps<{
  xp: number;
}>();

const currentLevel = computed(() => getLevelFromTotalXP(props.xp));

const xpNeeded = computed(() => getXPNeededAtThisLevel(currentLevel.value));

const xpEarnedForCurrentLevel = computed(() =>
  getXPEarnedAtThisLevel(props.xp),
);

const percentToNextLevel = computed(() => {
  return (xpEarnedForCurrentLevel.value / xpNeeded.value) * 100;
});
</script>
<style scoped></style>
