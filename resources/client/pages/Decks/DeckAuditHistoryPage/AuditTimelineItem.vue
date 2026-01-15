<template>
  <div class="border rounded-lg bg-white overflow-hidden">
    <button
      type="button"
      class="w-full px-4 py-3 flex items-center justify-between text-left hover:bg-gray-50 transition-colors"
      @click="isExpanded = !isExpanded"
    >
      <div class="flex items-center gap-3">
        <AuditEventBadge :event="audit.event" />
        <div>
          <p class="font-medium text-brand-maroon-900">
            {{ auditDescription }}
          </p>
          <p class="text-sm text-brand-maroon-900/60">
            {{ audit.user?.name ?? "System" }} &middot;
            {{ formatDate(audit.created_at) }}
          </p>
        </div>
      </div>
      <ChevronDownIcon
        class="size-5 text-brand-maroon-900/50 transition-transform"
        :class="{ 'rotate-180': isExpanded }"
      />
    </button>

    <div v-if="isExpanded" class="px-4 pb-4 border-t bg-gray-50">
      <AuditValuesDiff
        :old-values="audit.old_values"
        :new-values="audit.new_values"
        :auditable-type="audit.auditable_type"
        :event="audit.event"
      />
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from "vue";
import type { AuditRecord } from "@/types";
import { ChevronDownIcon } from "@radix-icons/vue";
import AuditEventBadge from "./AuditEventBadge.vue";
import AuditValuesDiff from "./AuditValuesDiff.vue";

const props = defineProps<{
  audit: AuditRecord;
}>();

const isExpanded = ref(false);

const auditDescription = computed(() => {
  const type = props.audit.auditable_type.toLowerCase();
  const event = props.audit.event;

  const eventLabels: Record<string, string> = {
    created: "created",
    updated: "updated",
    deleted: "deleted",
    restored: "restored",
  };

  return `${type.charAt(0).toUpperCase() + type.slice(1)} ${eventLabels[event] ?? event}`;
});

function formatDate(dateString: string): string {
  return new Date(dateString).toLocaleString();
}
</script>
