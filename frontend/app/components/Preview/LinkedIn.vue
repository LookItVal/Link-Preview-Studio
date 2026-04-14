<template>
  <div class="w-[526px] max-w-full overflow-hidden rounded-lg border border-[#8c8c8c33] bg-white font-[system-ui,-apple-system,BlinkMacSystemFont,'Segoe_UI',Helvetica,Arial,sans-serif] [-webkit-font-smoothing:antialiased] [-moz-osx-font-smoothing:grayscale] p-3 mx-auto">
    <div class="flex items-stretch">
      <div class="shrink-0 w-32 h-[72px] relative overflow-hidden rounded-lg">
        <div class="w-full h-full relative">
          <img class="h-full w-full absolute top-0 object-cover block" :src="image">
        </div>
      </div>
      <div class="flex-1 flex flex-col justify-center pl-3 min-w-0">
        <div class="text-[14px] font-semibold leading-5 text-[#000000e6] line-clamp-2 wrap-break-words mb-1">
          {{ title }}
        </div>
        <div class="text-[12px] font-normal leading-4 text-[#00000099] truncate">
          {{ hostname }}
        </div>
      </div>
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