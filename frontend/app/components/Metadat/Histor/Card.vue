<template>
  <UICard depth="item" :opacity="0.5" class="w-full">
    <div class="space-y-2 px-(--xs-em) py-2">
      <div class="flex items-center gap-2">
        <div class="min-w-0 flex-1 flex content-center items-start flex-col">
          <p class="truncate text-sm w-full">
            {{ entry.url }}
          </p>
          <span
            class="shrink-0 text-xs font-semibold py-0.5 rounded-full"
            :class="entry.status === 'success' ? 'text-green' : 'text-red'"
          >
            {{ entry.status }}
          </span>
        </div>
        <UICard depth="item" :opacity="0.5" class="flex items-center gap-1 p-(--xxxs-em)">
          <button
            v-for="option in socialOptions"
            :key="option.platform"
            type="button"
            class="min-w-8"
            :aria-label="`Toggle ${option.label} preview`"
            :aria-pressed="activePreview === option.platform"
            @click="togglePreview(option.platform)"
          >
            <UICard
              :depth="activePreview === option.platform ? 'surface' : 'overlay'"
              :opacity="activePreview === option.platform ? 0.55 : 0"
              class="flex h-8 w-8 items-center justify-center"
            >
              <Icon :name="option.icon" class="text-sm" />
            </UICard>
          </button>
        </UICard>
      </div>

      <MetadataHistoryPreviewCard
        v-if="activePreview"
        :entry="entry"
        :platform="activePreview"
        @close="closeCurrentPreview"
      />
    </div>
  </UICard>
</template>

<script setup lang="ts">
import type { HistoryEntry } from '~/composables/history'

type SocialPlatform = 'linkedin' | 'slack' | 'facebook' | 'twitter'

const socialOptions: Array<{ platform: SocialPlatform, label: string, icon: string }> = [
  { platform: 'linkedin', label: 'LinkedIn', icon: 'mdi:linkedin' },
  { platform: 'slack', label: 'Slack', icon: 'mdi:slack' },
  { platform: 'facebook', label: 'Facebook', icon: 'mdi:facebook' },
  { platform: 'twitter', label: 'Twitter', icon: 'mdi:twitter' },
]

const activePreview = ref<SocialPlatform | null>(null)

function togglePreview(platform: SocialPlatform) {
  activePreview.value = activePreview.value === platform ? null : platform
}

function closeCurrentPreview() {
  activePreview.value = null
}

defineExpose({
  activePreview,
  closeCurrentPreview,
})

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
