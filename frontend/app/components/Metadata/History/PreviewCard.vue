<template>
  <div ref="panel" :class="`space-y-2 p-(--xs-em) bg-(--color-crust) rounded-(--xs-em)`">
    <div ref="content" class="space-y-2">
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
  </div>
</template>

<script setup lang="ts">
import gsap from 'gsap'
import type { Component } from 'vue'
import type { HistoryEntry } from '~/composables/history'
import PreviewFacebook from '~/components/Preview/Facebook.vue'
import PreviewLinkedIn from '~/components/Preview/LinkedIn.vue'
import PreviewSlack from '~/components/Preview/Slack.vue'
import PreviewTwitter from '~/components/Preview/Twitter.vue'

const panel = ref<HTMLElement | null>(null)
const content = ref<HTMLElement | null>(null)

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

function setContentHidden(): gsap.core.Timeline {
  const timeline = gsap.timeline()
  timeline.set(panel.value, {
    opacity: 0,
    scaleY: 0.97,
    transformOrigin: 'top center'
  })
  timeline.set(content.value, {
    opacity: 0,
    filter: 'blur(10px)'
  })
  return timeline
}

function revealContent(): gsap.core.Timeline {
  const timeline = gsap.timeline()
  timeline.to(panel.value, {
    opacity: 1,
    scaleY: 1,
    duration: 0.35,
    ease: 'power1.inOut'
  })
  timeline.to(content.value, {
    opacity: 1,
    filter: 'blur(0px)',
    duration: 0.35,
    ease: 'power1.inOut'
  }, 0.05)
  return timeline
}

function removeContent(): gsap.core.Timeline {
  const timeline = gsap.timeline()
  timeline.to(content.value, {
    opacity: 0,
    filter: 'blur(10px)',
    duration: 0.25,
    ease: 'power1.inOut'
  })
  timeline.to(panel.value, {
    opacity: 0,
    scaleY: 0.97,
    duration: 0.25,
    ease: 'power1.inOut'
  }, 0)
  return timeline
}

defineExpose({
  panel,
  setContentHidden,
  revealContent,
  removeContent,
})
</script>
