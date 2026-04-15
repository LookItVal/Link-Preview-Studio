<template>
  <div
    ref="backdrop"
    class="fixed inset-0 z-60 flex items-center justify-center bg-(--color-crust)/70 p-4"
    role="dialog"
    aria-modal="true"
    aria-labelledby="details-modal-title"
    @click.self="$emit('close')"
  >
    <UICard
      ref="card"
      depth="item"
      :opacity="0.7"
      class="w-full max-w-2xl p-(--xs-em)"
      :data-flip-id="flipId"
    >
      <div ref="content" class="space-y-3 p-(--xs-em)">
        <div class="flex items-center justify-between">
          <h2 id="details-modal-title" class="text-base font-semibold text-(--color-text)">
            Response Details
          </h2>
          <button
            type="button"
            aria-label="Close details"
            class="cursor-pointer flex h-6 w-6 items-center justify-center rounded-full text-(--color-subtext0) hover:text-(--color-text) transition-all duration-200"
            @click="$emit('close')"
          >
            <Icon name="mdi:close" class="text-sm" />
          </button>
        </div>
        <pre class="overflow-auto max-h-[60vh] rounded-lg bg-(--color-surface-100) p-(--xs-em) text-xs text-(--color-text) font-mono whitespace-pre-wrap break-all"><code>{{ formattedJson }}</code></pre>
      </div>
    </UICard>
  </div>
</template>

<script setup lang="ts">
import gsap from 'gsap'
import type { HistoryEntry } from '~/composables/history'

const backdrop = ref<HTMLElement | null>(null)
const card = ref<HTMLElement | null>(null)
const content = ref<HTMLElement | null>(null)

const props = defineProps<{
  entry: HistoryEntry
  flipId: string
}>()


function setContentHidden(): gsap.core.Timeline {
  const timeline = gsap.timeline();
  timeline.set(content.value, {
    opacity: 0,
    filter: 'blur(10px)'
  });
  return timeline;
}

function setContentVisible(): gsap.core.Timeline {
  const timeline = gsap.timeline();
  timeline.set(content.value, {
    opacity: 1,
    filter: 'blur(0px)'
  });
  return timeline;
}

function revealContent(): gsap.core.Timeline {
  const timeline = gsap.timeline();
  timeline.to(content.value, {
    opacity: 1,
    filter: 'blur(0px)',
    duration: 0.5,
    ease: 'power1.inOut'
  });
  return timeline;
}

function removeContent(): gsap.core.Timeline {
  const timeline = gsap.timeline();
  timeline.to(content.value, {
    opacity: 0,
    filter: 'blur(10px)',
    duration: 0.5,
    ease: 'power1.inOut'
  });
  return timeline;
}


defineEmits<{
  close: []
}>()

defineExpose({
  backdrop,
  card,
  setContentHidden,
  setContentVisible,
  revealContent,
  removeContent,
})


const formattedJson = computed(() => JSON.stringify(props.entry.response, null, 2))
</script>
