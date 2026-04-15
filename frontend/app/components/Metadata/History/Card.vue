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
              class="min-w-(--s-em) aspect-square cursor-pointer flex shrink-0 items-center justify-center rounded-full text-(--color-subtext0) hover:text-(--color-red) transition-all duration-200"
              @click="deleteEntry"
            >
              <Icon name="mdi:close" class="text-[1rem]" />
            </button>
          </UICard>
            <div class="min-w-0 flex-1 flex content-center items-start flex-col">
              <div class="flex w-full items-center gap-2">
                <p class="truncate sm:text-[0.8rem] text-[0.56em] w-full">
                  {{ entry.url }}
                </p>
              </div>
              <span
                class="shrink-0 font-semibold pt-[0.1rem] sm:text-[0.6rem] text-[0.5rem] rounded-full"
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
                  class="cursor-pointer flex h-(--s-em) w-(--s-em) shrink-0 items-center justify-center rounded-full text-(--color-subtext0) hover:text-(--color-text) transition-all duration-200"
                  @click="copyUrl"
                >
                  <Icon name="mdi:content-copy" class="text-[1rem]" />
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
                  class="absolute top-21/20 right-1/2 translate-x-1/2 mt-1 whitespace-nowrap rounded-full bg-(--color-surface-100) px-2 py-1 text-xs text-(--color-text) shadow-md z-100 pointer-events-none"
                >
                  URL copied to clipboard
                </span>
              </Transition>
            </div>

            <UICard
              v-if="entry.status === 'success'"
              depth="item"
              :opacity="0.5"
              class="relative flex items-center gap-1 p-(--xxxs-em)"
            >
              <div
                ref="previewIndicator"
                aria-hidden="true"
                class="pointer-events-none absolute left-0 top-0 z-0 rounded-full bg-(--color-surface-300) opacity-0"
              />
              <button
                v-for="option in socialOptions"
                :key="option.platform"
                type="button"
                :ref="(el) => setSocialButtonRef(option.platform, el)"
                class="relative z-1 min-w-(--s-em) cursor-pointer aspect-square flex shrink-0 items-center justify-center rounded-full text-(--color-subtext0) hover:text-(--color-text) transition-all duration-200"
                :aria-label="`Toggle ${option.label} preview`"
                :aria-pressed="activePreview === option.platform"
                @click="togglePreview(option.platform)"
              >
                <span class="flex h-(--s-em) w-(--s-em) items-center justify-center">
                  <Icon :name="option.icon" class="text-[1rem]" />
                </span>
              </button>
            </UICard>

            <UICard depth="item" :opacity="0.5" class="flex items-center gap-1 p-(--xxxs-em)">
              <button
                type="button"
                aria-label="View response details"
                class="min-w-(--s-em) aspect-square cursor-pointer flex shrink-0 items-center justify-center rounded-full text-(--color-subtext0) hover:text-(--color-text) transition-all duration-200"
                @click="openDetails"
              >
                <Icon name="mdi:code-braces" class="text-[1rem]" />
              </button>
            </UICard>
          </div>

          <MetadataHistoryPreviewCard
            v-if="activePreview"
            ref="previewCard"
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

gsap.registerPlugin(Flip)

type SocialPlatform = 'linkedin' | 'slack' | 'facebook' | 'twitter'

const props = withDefaults(defineProps<{
  entry: HistoryEntry
  initialActivePreview?: SocialPlatform | null
}>(), {
  initialActivePreview: null,
})

const socialOptions: Array<{ platform: SocialPlatform, label: string, icon: string }> = [
  { platform: 'linkedin', label: 'LinkedIn', icon: 'mdi:linkedin' },
  { platform: 'slack', label: 'Slack', icon: 'mdi:slack' },
  { platform: 'facebook', label: 'Facebook', icon: 'mdi:facebook' },
  { platform: 'twitter', label: 'Twitter', icon: 'mdi:twitter' },
]

const activePreview = ref<SocialPlatform | null>(props.initialActivePreview)
const showCopied = ref(false)
const showDetails = ref(false)
const container = ref<HTMLElement | null>(null)
const cardSurface = ref<InstanceType<typeof UICard> | null>(null)
const details = ref<typeof MetadataHistoryDetails | null>(null)
const previewIndicator = ref<HTMLElement | null>(null)
const socialButtons = ref<Partial<Record<SocialPlatform, HTMLElement>>>({})
const previewCard = ref<{
  setContentHidden?: () => gsap.core.Timeline
  revealContent?: () => gsap.core.Timeline
  removeContent?: () => gsap.core.Timeline
} | null>(null)
const { removeEntry } = useHistory()

const ctx = ref<gsap.Context | null>(null)

let openTimeline: gsap.core.Timeline | null = null
let closeTimeline: gsap.core.Timeline | null = null

function waitForAnimation(animation?: gsap.core.Animation | null): Promise<void> {
  if (!animation) {
    return Promise.resolve()
  }

  return new Promise((resolve) => {
    animation.eventCallback('onComplete', () => resolve())
  })
}

function setSocialButtonRef(platform: SocialPlatform, element: Element | { $el?: Element } | null) {
  const resolvedElement = element instanceof HTMLElement
    ? element
    : element && typeof element === 'object' && '$el' in element && element.$el instanceof HTMLElement
      ? element.$el
      : null

  if (resolvedElement) {
    socialButtons.value[platform] = resolvedElement
    return
  }

  delete socialButtons.value[platform]
}

function getIndicatorTarget(platform: SocialPlatform) {
  const indicatorEl = previewIndicator.value
  const buttonEl = socialButtons.value[platform]
  const indicatorParent = indicatorEl?.offsetParent
  if (!indicatorEl || !buttonEl || !indicatorParent) {
    return null
  }

  const parentRect = (indicatorParent as HTMLElement).getBoundingClientRect()
  const buttonRect = buttonEl.getBoundingClientRect()

  return {
    x: buttonRect.left - parentRect.left,
    y: buttonRect.top - parentRect.top,
    width: buttonRect.width,
    height: buttonRect.height,
  }
}

function animateIndicatorTo(platform: SocialPlatform | null): Promise<void> {
  const indicatorEl = previewIndicator.value
  if (!indicatorEl || !ctx.value) {
    return Promise.resolve()
  }

  let animation: gsap.core.Animation | null = null

  ctx.value.add(() => {
    if (!platform) {
      animation = gsap.to(indicatorEl, {
        opacity: 0,
        duration: 0.2,
        ease: 'power1.inOut',
      })
      return
    }

    const target = getIndicatorTarget(platform)
    if (!target) {
      return
    }

    animation = gsap.to(indicatorEl, {
      x: target.x,
      y: target.y,
      width: target.width,
      height: target.height,
      opacity: 1,
      duration: 0.35,
      ease: 'power1.inOut',
    })
  })

  return waitForAnimation(animation)
}

function syncIndicatorPosition(platform: SocialPlatform | null) {
  const indicatorEl = previewIndicator.value
  if (!indicatorEl || !platform) {
    return
  }

  const target = getIndicatorTarget(platform)
  if (!target) {
    return
  }

  gsap.set(indicatorEl, {
    x: target.x,
    y: target.y,
    width: target.width,
    height: target.height,
    opacity: 1,
  })
}

function handleResize() {
  syncIndicatorPosition(activePreview.value)
}

function animatePreviewResize(state: Flip.FlipState | null, sourceEl?: HTMLElement): Promise<void> {
  if (!ctx.value || !sourceEl || !state) {
    return Promise.resolve()
  }

  let animation: gsap.core.Animation | null = null

  ctx.value.add(() => {
    animation = Flip.from(state, {
      duration: 0.45,
      ease: 'power2.inOut',
      nested: true,
      targets: sourceEl,
    })
  })

  return waitForAnimation(animation)
}

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

async function togglePreview(platform: SocialPlatform) {
  const sourceEl = cardSurface.value?.$el as HTMLElement | undefined
  const nextPreview = activePreview.value === platform ? null : platform

  if (activePreview.value && previewCard.value?.removeContent) {
    await waitForAnimation(previewCard.value.removeContent())
  }

  const cardState = sourceEl ? Flip.getState(sourceEl) : null

  activePreview.value = nextPreview
  await nextTick()

  if (nextPreview && previewCard.value?.setContentHidden) {
    previewCard.value.setContentHidden()
  }

  await Promise.all([
    animateIndicatorTo(nextPreview),
    animatePreviewResize(cardState as Flip.FlipState, sourceEl),
  ])

  if (nextPreview && previewCard.value?.revealContent) {
    await waitForAnimation(previewCard.value.revealContent())
  }
}

async function closeCurrentPreview() {
  if (!activePreview.value) return
  await togglePreview(activePreview.value)
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

  if (activePreview.value) {
    nextTick(() => syncIndicatorPosition(activePreview.value))
  }

  window.addEventListener('resize', handleResize)
})

onBeforeUnmount(() => {
  window.removeEventListener('resize', handleResize)
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
