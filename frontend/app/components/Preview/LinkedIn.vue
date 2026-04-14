<template>
  <div class="rounded-(--phi) border border-(--color-surface0) bg-(--color-base) p-(--xs-em)">
    <div class="space-y-1">
      <p class="text-xs text-(--color-subtext0)">
        {{ hostname }}
      </p>
      <p class="text-sm font-semibold text-(--color-text)">
        {{ title }}
      </p>
      <p class="line-clamp-2 text-xs text-(--color-subtext1)">
        {{ description }}
      </p>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { HistoryEntry } from '~/composables/history'

const props = defineProps<{
  entry: HistoryEntry
}>()

const hostname = computed(() => {
  try {
    return new URL(props.entry.url).hostname
  }
  catch {
    return props.entry.url
  }
})

const title = computed(() => {
  return props.entry.response?.data?.og?.title ||
         props.entry.response?.data?.twitter?.title ||
         props.entry.response?.data?.title ||
         'Untitled link'
})

const description = computed(() => {
  return props.entry.response?.data?.og?.description ||
         props.entry.response?.data?.twitter?.description ||
         props.entry.response?.data?.description ||
         'No description available for this result.'
})

const image = computed(() => {
  return props.entry.response?.data?.og?.image ||
         props.entry.response?.data?.twitter?.image ||
         props.entry.response?.data?.image ||
         ''
})
</script>