<template>
  <div>
    <div class='-outline-offset-1 relative w-[438px] max-w-full overflow-hidden rounded-[0.85714em] border border-[#e1e8ed] leading-[1.3em] text-black mx-auto'>
      <div class="bg-cover bg-center bg-no-repeat">
        <div class="w-full relative h-0" style="padding-top: 52.33%;">
          <img :src="image" :alt="title" class="h-full w-full absolute top-0 object-cover block" />
        </div>
      </div>
      <div class="absolute bottom-3 left-3 asd text-[13px]/5 text-white bg-black/77 px-2 rounded text-ellipsis max-w-[calc(100%-1.5rem)] whitespace-nowrap overflow-hidden">
        {{ title }}
      </div>
    </div>
    <div class="text-[#536471] text-[13px] w-[438px] max-w-full mx-auto">
      {{ `from ${hostname}` }}
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

const image = computed(() => {
  return props.entry.response?.data?.og?.image ||
         props.entry.response?.data?.twitter?.image ||
         props.entry.response?.data?.image ||
         ''
})
</script>