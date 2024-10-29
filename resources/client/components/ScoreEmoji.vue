<template>
  <div>
    <TooltipProvider>
      <Tooltip>
        <TooltipTrigger>
          <div class="text-sm">
            <span
              v-if="!score"
              class="text-xs font-sans bg-umn-gold-300 rounded-full px-2 leading-none flex items-center py-1"
              >New</span
            >
            <span v-else-if="score > 0.75">✅</span>
            <span v-else-if="score > 0.5">🫤</span>
            <span v-else-if="score > 0">❌</span>
            >
          </div>
        </TooltipTrigger>
        <TooltipContent>
          <div class="flex gap-2 p-1 justify-between">
            <Tuple
              label="Correct"
              labelClass="text-brand-oatmeal-500 text-right"
            >
              {{ percent }}%
            </Tuple>
            <Tuple
              label="Attempts"
              labelClass="text-brand-oatmeal-500 text-right"
              v-if="attempts"
            >
              {{ attempts }}
            </Tuple>
          </div>
          <div
            class="flex flex-col gap-1 mt-2 pt-2 border-t border-t-brand-oatmeal-50/50"
          >
            <p>✅ 75-100% correct</p>
            <p>🫤 50-75% correct</p>
            <p>❌ 0-50% correct</p>
          </div>
        </TooltipContent>
      </Tooltip>
    </TooltipProvider>
  </div>
</template>

<script setup lang="ts">
import {
  Tooltip,
  TooltipContent,
  TooltipProvider,
  TooltipTrigger,
} from "@/components/ui/tooltip";
import { computed } from "vue";
import Tuple from "./Tuple.vue";

const props = withDefaults(
  defineProps<{
    score: number | null; // 0-1
    attempts?: number;
  }>(),
  {
    attempts: undefined,
  },
);

const percent = computed(() => (props.score * 100).toFixed(2));
</script>

<style scoped></style>
