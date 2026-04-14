<template>
  <div class="flex font-['Noto_Sans','NotoSansJP','Slack-Lato','Lato','appleLogo',sans-serif] text-[15px] leading-[1.46666667] wrap-break-words antialiased">
    <div class="shrink-0 w-1 rounded-lg" :style="`background-color: ${themeColor};`" />
    <div class="px-3 max-w-[520px]">
      <div class="flex items-center">
        <img class="rounded-sm box-content h-4 w-4 mr-1.5 overflow-hidden align-middle" :src="favicon" :alt="sitename"/>
        <span class="font-['Noto_Sans','NotoSansJP','Slack-Lato','Lato','appleLogo',sans-serif] text-[15px] text-[#717274] flex-1 overflow-hidden wrap-break-words">
          {{ sitename }}
        </span>
      </div>
      <div class="font-['Noto_Sans','NotoSansJP','Slack-Lato','Lato','appleLogo',sans-serif] text-[15px] leading-[1.46666667] wrap-break-words antialiased text-[#0576b9] font-bold [font-variant-ligatures:common-ligatures] [font-feature-settings:'liga','clig']">
        {{ title }}
      </div>
      <div class="font-['Noto_Sans','NotoSansJP','Slack-Lato','Lato','appleLogo',sans-serif] text-[15px] leading-[1.46666667] wrap-break-words antialiased">
        {{ description }}
      </div>
      <div class="rounded-sm shadow-[inset_0_0_0_1px_rgba(0,0,0,0.1)] mt-[5px] max-w-[360px] h-[189px] bg-cover bg-center" :style="`background-image: url(${image});`" />
    </div>
  </div>
</template>

<script setup lang="ts">
import type { HistoryEntry } from '~/composables/history'

const props = defineProps<{
  entry: HistoryEntry
}>()

const sitename = computed(() => {
  return props.entry.response?.data?.og?.site_name ||
         props.entry.response?.data?.twitter?.site_name ||
         props.entry.response?.data?.site_name ||

         ''
})

const favicon = computed(() => {
  return props.entry.response?.data?.og?.favicon ||
         props.entry.response?.data?.twitter?.favicon ||
         props.entry.response?.data?.favicon ||
         ''
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
         ''
})

const image = computed(() => {
  return props.entry.response?.data?.og?.image ||
         props.entry.response?.data?.twitter?.image ||
         props.entry.response?.data?.image ||
         ''
})

const themeColor = computed(() => {
  return props.entry.response?.data?.og?.theme_color ||
         props.entry.response?.data?.twitter?.theme_color ||
         props.entry.response?.data?.theme_color ||
         '#e8e8e8'
})

</script>