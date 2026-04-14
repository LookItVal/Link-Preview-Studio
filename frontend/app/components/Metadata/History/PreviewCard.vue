<template>
  <div :class="`space-y-2 p-(--xs-em) bg-(--color-crust) rounded-(--xs-em)`">
    <div class="flex items-center justify-between">
      <p class="text-xs font-semibold tracking-wide uppercase text-(--color-subtext0)">
        {{ platformLabel }} preview
      </p>
      <button
        type="button"
        class="text-xs font-semibold rounded-full px-2 py-0.5 text-(--color-subtext0) hover:text-(--color-text)"
        @click="$emit('close')"
      >
        Close
      </button>
    </div>

    <component :is="previewComponent" :entry="entry" />
  </div>
</template>

<script setup lang="ts">
import type { Component } from 'vue'
import type { HistoryEntry } from '~/composables/history'
import PreviewFacebook from '~/components/Preview/Facebook.vue'
import PreviewLinkedIn from '~/components/Preview/LinkedIn.vue'
import PreviewSlack from '~/components/Preview/Slack.vue'
import PreviewTwitter from '~/components/Preview/Twitter.vue'

const { mode } = useColorMode()

type SocialPlatform = 'linkedin' | 'slack' | 'facebook' | 'twitter'

const props = defineProps<{
  entry: HistoryEntry
  platform: SocialPlatform
}>()

defineEmits<{
  close: []
}>()

const platformLabel = computed(() => {
  const labels: Record<SocialPlatform, string> = {
    linkedin: 'LinkedIn',
    slack: 'Slack',
    facebook: 'Facebook',
    twitter: 'Twitter',
  }

  return labels[props.platform]
})

const previewComponent = computed(() => {
  const components: Record<SocialPlatform, Component> = {
    linkedin: PreviewLinkedIn,
    slack: PreviewSlack,
    facebook: PreviewFacebook,
    twitter: PreviewTwitter,
  }

  return components[props.platform]
})
</script>
