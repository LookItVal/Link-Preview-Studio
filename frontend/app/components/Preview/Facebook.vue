<template>
  <div class="md:w-[524px] sm:w-[15em] w-[10em] max-w-full mx-auto">
    <div v-if="image" class="bg-cover bg-center bg-no-repeat">
      <div class="w-full relative h-0 aspect-[0.525]" style="padding-top: 52.5%;">
        <img class="h-full w-full absolute top-0 object-cover block" :src="image" :alt="title" />
      </div>
    </div>
    <div class="wrap-break-words bg-[#F0F2F5] px-4 pt-2.5 pb-1.5 antialiased font-[system-ui,-apple-system,BlinkMacSystemFont,Segoe_UI_Historic,Segoe_UI,Helvetica,Arial,sans-serif]">
      <div class="overflow-hidden truncate whitespace-nowrap text-[13px] uppercase leading-3 text-[#65686C] mb-[5px]">
        {{ hostname }}
      </div>
      <div class="block border-separate select-none overflow-hidden wrap-break-words text-left">
        <p class="truncate text-[17px] font-semibold leading-[21px] text-[#080809]">
          {{ title }}
        </p>
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

const jsonLd = computed(() => props.entry.response?.data?.jsonLd?.[0])

const title = computed(() => {
  return props.entry.response?.data?.og?.title ||
         props.entry.response?.data?.twitter?.title ||
         jsonLd.value?.headline ||
         jsonLd.value?.name ||
         props.entry.response?.data?.title ||
         'Untitled link'
})

const image = computed(() => {
  return props.entry.response?.data?.og?.image ||
         props.entry.response?.data?.twitter?.image ||
         jsonLd.value?.image ||
         props.entry.response?.data?.image ||
         ''
})
</script>
