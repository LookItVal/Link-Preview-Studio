<template>
  <div ref="container" class="w-full">
    <UICard
      ref="cardSurface"
      depth="item"
      :opacity="0.5"
      class="w-full"
      :data-flip-id="flipId"
    >
      <div class="flex items-start gap-2 p-(--xxxs-em)">
        <div class="min-w-0 flex-1 space-y-2">
          <div class="flex items-center gap-2">
          <UICard depth="item" :opacity="0.5" class="flex items-center gap-1 p-(--xxxs-em)">
            <button
              type="button"
              aria-label="Delete history item"
              class="min-w-6 aspect-square cursor-pointer flex shrink-0 items-center justify-center rounded-full text-(--color-subtext0) hover:text-(--color-red) transition-all duration-200"
              @click="deleteEntry"
            >
              <Icon name="mdi:close" class="text-sm" />
            </button>
          </UICard>
            <div class="min-w-0 flex-1 flex content-center items-start flex-col">
              <div class="flex w-full items-center gap-2">
                <p class="truncate text-sm w-full">
                  {{ entry.url }}
                </p>
              </div>
              <span
                class="shrink-0 text-xs font-semibold py-0.5 rounded-full"
                :class="entry.status === 'success' ? 'text-green' : 'text-red'"
              >
                {{ statusText }}
              </span>
            </div>

            <div class="relative">
              <UICard depth="item" :opacity="0.5" class="flex items-center gap-1 p-(--xxxs-em)">
                <button
                  type="button"
                  aria-label="Copy URL"
                  class="cursor-pointer flex h-6 w-6 shrink-0 items-center justify-center rounded-full text-(--color-subtext0) hover:text-(--color-text) transition-all duration-200"
                  @click="copyUrl"
                >
                  <Icon name="mdi:content-copy" class="text-sm" />
                </button>
              </UICard>
              <Transition
                enter-active-class="transition-all duration-150 ease-out"
                leave-active-class="transition-all duration-200 ease-in"
                enter-from-class="opacity-0 -translate-y-1"
                leave-to-class="opacity-0 -translate-y-1"
              >
                <span
                  v-if="showCopied"
                  class="absolute top-21/20 right-1/2 translate-x-1/2 mt-1 whitespace-nowrap rounded-full bg-(--color-surface-100) px-2 py-1 text-xs text-(--color-text) shadow-md z-10 pointer-events-none"
                >
                  URL copied to clipboard
                </span>
              </Transition>
            </div>

            <UICard
              v-if="entry.status === 'success'"
              depth="item"
              :opacity="0.5"
              class="flex items-center gap-1 p-(--xxxs-em)"
            >
              <button
                v-for="option in socialOptions"
                :key="option.platform"
                type="button"
                class="min-w-6 cursor-pointer aspect-square flex shrink-0 items-center justify-center rounded-full text-(--color-subtext0) hover:text-(--color-text) transition-all duration-200"
                :aria-label="`Toggle ${option.label} preview`"
                :aria-pressed="activePreview === option.platform"
                @click="togglePreview(option.platform)"
              >
                <UICard
                  :depth="activePreview === option.platform ? 'surface' : 'overlay'"
                  :opacity="activePreview === option.platform ? 0.55 : 0"
                  class="flex h-6 w-6 items-center justify-center"
                >
                  <Icon :name="option.icon" class="text-sm" />
                </UICard>
              </button>
            </UICard>

            <UICard depth="item" :opacity="0.5" class="flex items-center gap-1 p-(--xxxs-em)">
              <button
                type="button"
                aria-label="View response details"
                class="min-w-6 aspect-square cursor-pointer flex shrink-0 items-center justify-center rounded-full text-(--color-subtext0) hover:text-(--color-text) transition-all duration-200"
                @click="openDetails"
              >
                <Icon name="mdi:code-braces" class="text-sm" />
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
      </div>
    </UICard>

    <Teleport to="body">
      <MetadataHistoryDetails
        v-if="showDetails"
        ref="details"
        :entry="entry"
        :flip-id="flipId"
        @close="closeDetails"
      />
    </Teleport>
  </div>
</template>

<script setup lang="ts">
import gsap from 'gsap'
import Flip from 'gsap/Flip'
import type { HistoryEntry } from '~/composables/history'
import MetadataHistoryDetails from '~/components/Metadata/History/Details.vue'
import UICard from '~/components/UI/Card.vue'

type SocialPlatform = 'linkedin' | 'slack' | 'facebook' | 'twitter'

const socialOptions: Array<{ platform: SocialPlatform, label: string, icon: string }> = [
  { platform: 'linkedin', label: 'LinkedIn', icon: 'mdi:linkedin' },
  { platform: 'slack', label: 'Slack', icon: 'mdi:slack' },
  { platform: 'facebook', label: 'Facebook', icon: 'mdi:facebook' },
  { platform: 'twitter', label: 'Twitter', icon: 'mdi:twitter' },
]

const activePreview = ref<SocialPlatform | null>(null)
const showCopied = ref(false)
const showDetails = ref(false)
const container = ref<HTMLElement | null>(null)
const cardSurface = ref<InstanceType<typeof UICard> | null>(null)
const details = ref<typeof MetadataHistoryDetails | null>(null)
const { removeEntry } = useHistory()

const ctx = ref<gsap.Context | null>(null)

let openTimeline: gsap.core.Timeline | null = null
let closeTimeline: gsap.core.Timeline | null = null

const props = defineProps<{
  entry: HistoryEntry
}>()

const flipId = computed(() => `history-details-${props.entry.timestamp}`)

const statusText = computed(() => {
  if (props.entry.status === 'success') {
    return 'success'
  }

  const rawError = props.entry.response?.error
  if (typeof rawError === 'string') {
    return rawError
  }

  const description = typeof rawError?.message === 'string' && rawError.message.trim()
    ? rawError.message
    : 'error'

  return description
})

async function openDetails() {
  if (showDetails.value) return

  const sourceEl = cardSurface.value?.$el as HTMLElement | undefined
  const state = sourceEl ? Flip.getState(sourceEl) : null

  showDetails.value = true
  await nextTick()

  if (!ctx.value || !details.value?.$el || !state) return

  openTimeline?.kill()
  openTimeline = null

  const detailsEl = details.value.$el as HTMLElement
  const backdropEl = details.value.backdrop as HTMLElement | null
  const detailsCardEl = details.value.card?.$el as HTMLElement | undefined

  // ctx.add registers tweens for cleanup, but the return value is the context
  ctx.value.add(() => {
    // Assign to the outer variable directly
    openTimeline = gsap.timeline()

    if (details.value?.setContentHidden) {
      openTimeline.add(details.value.setContentHidden())
    }
    openTimeline.set(detailsEl, { opacity: 1 })
    if (backdropEl) {
      openTimeline.set(backdropEl, { opacity: 0 })
      openTimeline.to(backdropEl, { opacity: 1, duration: 0.35, ease: 'power1.out' }, 0)
    }
    openTimeline.add(Flip.from(state, {
      absolute: true, duration: 0.5, ease: 'power2.inOut', nested: true, targets: detailsCardEl,
    }))
    if (details.value?.revealContent) {
      openTimeline.add(details.value.revealContent(), '>-0.15')
    }
  })
}

async function closeDetails() {
  if (!showDetails.value) return

  const sourceEl = cardSurface.value?.$el as HTMLElement | undefined
  const state = sourceEl ? Flip.getState(sourceEl) : null

  if (!ctx.value || !details.value?.$el || !state) return

  closeTimeline?.kill()
  closeTimeline = null

  const detailsEl = details.value.$el as HTMLElement
  const backdropEl = details.value.backdrop as HTMLElement | null
  const detailsCardEl = details.value.card?.$el as HTMLElement | undefined

  ctx.value.add(() => {
    closeTimeline = gsap.timeline()
    if (details.value?.removeContent) {
      closeTimeline.add(details.value.removeContent())
    }
    closeTimeline.add(Flip.to(state, {
      absolute: true, duration: 0.5, ease: 'power2.inOut', nested: true, targets: detailsCardEl,
    }))
    if (backdropEl) {
      closeTimeline.to(backdropEl, {
        opacity: 0,
        duration: 0.35,
        ease: 'power1.out'
      })
    }
    closeTimeline.call(() => {
      showDetails.value = false
      closeTimeline?.kill()
      closeTimeline = null
    })
  })
}

function togglePreview(platform: SocialPlatform) {
  activePreview.value = activePreview.value === platform ? null : platform
}

function closeCurrentPreview() {
  activePreview.value = null
}

function deleteEntry() {
  removeEntry(props.entry)
}

async function copyUrl() {
  if (!import.meta.client || !navigator?.clipboard?.writeText) {
    return
  }

  await navigator.clipboard.writeText(props.entry.url)
  showCopied.value = true
  setTimeout(() => { showCopied.value = false }, 2000)
}

onMounted(() => {
  ctx.value = gsap.context(() => {}, container.value ?? undefined)
})

onBeforeUnmount(() => {
  ctx.value?.revert()
})

defineExpose({
  activePreview,
  closeCurrentPreview,
})
</script>

<style scoped>
.text-green {
  color: var(--color-green);
}
.text-red {
  color: var(--color-red);
}


.toast-enter-from,
.toast-leave-to {
  opacity: 0;
  transform: translateY(-4px);
}
</style>
