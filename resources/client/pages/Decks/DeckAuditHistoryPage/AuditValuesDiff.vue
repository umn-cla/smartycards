<template>
  <div class="mt-4">
    <div
      v-if="changedFields.length === 0"
      class="text-sm text-brand-maroon-900/50"
    >
      No field changes recorded.
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-[auto_1fr_1fr]">
      <!-- Header row (hidden on mobile) -->
      <div
        class="hidden md:contents text-xs font-semibold text-brand-maroon-900/70 uppercase tracking-wide"
      >
        <div class="p-2">Field</div>
        <div class="p-2">Before</div>
        <div class="p-2">After</div>
      </div>

      <!-- Data rows -->
      <template v-for="field in changedFields" :key="field">
        <div
          class="md:contents border-t border-brand-maroon-900/10 pt-3 md:pt-0 md:border-0 first:border-0 first:pt-0"
        >
          <!-- Field name -->
          <div
            class="py-1 md:py-3 text-sm font-medium text-brand-maroon-900/70 md:border-t md:border-brand-maroon-900/10 px-3"
          >
            {{ formatFieldName(field) }}
          </div>

          <!-- Before value -->
          <div
            class="py-1 md:py-3 md:border-t md:border-brand-maroon-900/10 px-3"
          >
            <p class="text-xs text-brand-maroon-900/50 mb-1 md:hidden">
              Before
            </p>
            <div
              class="p-3 rounded text-sm"
              :class="
                hasOldValue
                  ? 'bg-red-50 border border-red-200'
                  : 'bg-neutral-100 border border-neutral-200'
              "
            >
              <span v-if="!hasOldValue" class="text-brand-maroon-900/40 italic">
                N/A
              </span>
              <CardContentPreview
                v-else-if="isCardContentField(field)"
                :blocks="parseCardContent(oldValues?.[field])"
              />
              <span v-else class="whitespace-pre-wrap break-words">{{
                formatValue(oldValues?.[field])
              }}</span>
            </div>
          </div>

          <!-- After value -->
          <div
            class="py-1 md:py-3 md:border-t md:border-brand-maroon-900/10 px-3"
          >
            <p class="text-xs text-brand-maroon-900/50 mb-1 md:hidden">After</p>
            <div
              class="p-3 rounded text-sm"
              :class="
                hasNewValue
                  ? 'bg-green-50 border border-green-200'
                  : 'bg-gray-100 border border-gray-200'
              "
            >
              <span v-if="!hasNewValue" class="text-brand-maroon-900/40 italic">
                N/A
              </span>
              <CardContentPreview
                v-else-if="isCardContentField(field)"
                :blocks="parseCardContent(newValues?.[field])"
              />
              <span v-else class="whitespace-pre-wrap break-words">{{
                formatValue(newValues?.[field])
              }}</span>
            </div>
          </div>
        </div>
      </template>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from "vue";
import type { AuditableType, AuditEvent, ContentBlock } from "@/types";
import CardContentPreview from "./CardContentPreview.vue";

const props = defineProps<{
  oldValues: Record<string, unknown> | null;
  newValues: Record<string, unknown> | null;
  auditableType: AuditableType;
  event: AuditEvent;
}>();

// For "created" events, there's no old value; for "deleted" events, there's no new value
const hasOldValue = computed(() => props.event !== "created");
const hasNewValue = computed(() => props.event !== "deleted");

const changedFields = computed(() => {
  const allFields = new Set([
    ...Object.keys(props.oldValues ?? {}),
    ...Object.keys(props.newValues ?? {}),
  ]);
  return Array.from(allFields).filter((field) => !isIgnoredField(field));
});

function isIgnoredField(field: string): boolean {
  // Skip internal/technical fields that aren't meaningful to users
  const ignoredFields = [
    "id",
    "deck_id",
    "created_at",
    "updated_at",
    "deleted_at",
  ];
  return ignoredFields.includes(field);
}

function formatFieldName(field: string): string {
  return field
    .replace(/_/g, " ")
    .replace(/\b\w/g, (char) => char.toUpperCase());
}

function isCardContentField(field: string): boolean {
  return (
    props.auditableType === "Card" && (field === "front" || field === "back")
  );
}

function parseCardContent(value: unknown): ContentBlock[] {
  if (!value) {
    return [];
  }
  if (typeof value === "string") {
    try {
      return JSON.parse(value) as ContentBlock[];
    } catch {
      return [];
    }
  }
  if (Array.isArray(value)) {
    return value as ContentBlock[];
  }
  return [];
}

function formatValue(value: unknown): string {
  if (value === null || value === undefined) {
    return "(empty)";
  }
  if (typeof value === "boolean") {
    return value ? "Yes" : "No";
  }
  if (typeof value === "object") {
    return JSON.stringify(value, null, 2);
  }
  return String(value);
}
</script>
