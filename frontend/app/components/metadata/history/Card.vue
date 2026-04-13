<template>
  <UICard depth="item" class="w-full">
    <div class="flex items-center justify-between px-(--xs-em) py-2">
      <p class="truncate text-sm">
        {{ entry.url }}
      </p>
      <span
        class="shrink-0 ml-4 text-xs font-semibold px-2 py-0.5 rounded-full"
        :class="entry.status === 'success' ? 'text-green' : 'text-red'"
      >
        {{ entry.status }}
      </span>
    </div>
  </UICard>
</template>

<script setup lang="ts">
import type { HistoryEntry } from '~/composables/history'

defineProps<{
  entry: HistoryEntry
}>()
</script>

<style scoped>
.text-green {
  color: var(--color-green);
}
.text-red {
  color: var(--color-red);
}
</style>
