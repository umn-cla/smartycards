<template>
  <Badge :variant="variant" :class="badgeClass">
    {{ label }}
  </Badge>
</template>

<script setup lang="ts">
import { computed } from "vue";
import { Badge } from "@/components/ui/badge";
import type { AuditEvent } from "@/types";

const props = defineProps<{
  event: AuditEvent;
}>();

const variant = computed(() => "outline" as const);

const badgeClass = computed(() => {
  switch (props.event) {
    case "created":
      return "border-green-200 bg-green-100 text-green-700";
    case "updated":
      return "border-blue-200 bg-blue-100 text-blue-700";
    case "deleted":
      return "border-red-200 bg-red-100 text-red-700";
    case "restored":
      return "border-amber-200 bg-amber-100 text-amber-700";
    default:
      return "";
  }
});

const label = computed(() => {
  switch (props.event) {
    case "created":
      return "Created";
    case "updated":
      return "Updated";
    case "deleted":
      return "Deleted";
    case "restored":
      return "Restored";
    default:
      return props.event;
  }
});
</script>
