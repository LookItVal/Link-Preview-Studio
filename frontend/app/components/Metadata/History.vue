<template>
  <div class="w-full max-w-xl space-y-2 pt-(--xs-em)" data-flip-id="history-bg">
    <MetadataHistoryCard
      v-for="entry in visibleHistory"
      :key="entry.timestamp"
      :entry="entry"
      :data-flip-id="`card-${entry.timestamp}`"
    />
    <MetadataHistoryCard
      v-if="leavingEntry"
      :key="leavingEntry.timestamp"
      :entry="leavingEntry"
      :data-flip-id="`card-${leavingEntry.timestamp}`"
      class="absolute"
    />
  </div>
</template>

<script setup lang="ts">
import gsap from 'gsap'
import Flip from 'gsap/Flip'

const { history } = useHistory()
const visibleHistory = ref([...history.value])
const leavingEntry = ref<any | null>(null)
const ctx = ref<gsap.Context | null>(null)

watch(() => history.value, async (newHistory, oldHistory) => {
  const newItems = newHistory.filter(
    newItem => !oldHistory.some(oldItem => isSameEntry(newItem, oldItem)),
  )
  if (newItems.length) {
    addHistoryEntry(newHistory, oldHistory)
  }
  const leaving = oldHistory.filter(
    item => !newHistory.some(newItem => isSameEntry(item, newItem)))
  if (leaving.length) {
    removeHistoryEntry(newHistory, oldHistory)
  }
})

function isSameEntry(a: any, b: any) {
  return a.timestamp === b.timestamp && a.url === b.url
}

function addHistoryEntry(newHistory: any[], oldHistory: any[]) {
  const newItems = newHistory.filter(
    newItem => !oldHistory.some(oldItem => isSameEntry(newItem, oldItem)),
  )

  const movingItems = newHistory.filter(
    newItem => oldHistory.some(oldItem => isSameEntry(newItem, oldItem)),
  )

  if (!newItems.length) {
    throw new Error('No new history items to add')
  }

  const movingSelectors = movingItems
    .map(item => `[data-flip-id="card-${item.timestamp}"]`)
  const newItemsSelectors = newItems
    .map(item => `[data-flip-id="card-${item.timestamp}"]`)


  ctx.value?.add(async () => {
    const movingDuration = 0.5
    const movingStagger = -0.05
    const movingTotalDuration = movingDuration + Math.abs(movingStagger) * Math.max(movingSelectors.length - 1, 0)

    const timeline = gsap.timeline({ onComplete: () => {
      visibleHistory.value = [...newHistory]
    } })
    const flipState = Flip.getState(movingSelectors)
    const backgroundState = Flip.getState('[data-flip-id="history-bg"]')
    visibleHistory.value = [...newItems, ...oldHistory]
    await nextTick()

    timeline.add(Flip.from(backgroundState, {
      duration: movingTotalDuration,
      ease: 'power2.out',
    }), 0)
    if (oldHistory.length) {
      timeline.add(Flip.from(flipState, {
        duration: movingDuration,
        stagger: movingStagger,
        ease: 'power1.inOut',
        absolute: true,
        targets: movingSelectors
      }), 0)
    }
    timeline.from(newItemsSelectors, {
      scale: 0,
      duration: 0.5,
      ease: 'back.out(1.3)'
    }, '>-0.1')
  })
}

function addHistoryWithOverflow(newHistory: any[], oldHistory: any[]) {
  // Implementation for adding history with overflow handling
}


function removeHistoryEntry(newHistory: any[], oldHistory: any[]) {
  const leaving = oldHistory.filter(
    item => !newHistory.some(newItem => isSameEntry(item, newItem))
  )
  if (!leaving.length) {
    throw new Error('No history items to remove')
  }
  const movingSelectors = newHistory
    .filter(item => oldHistory.some(oldItem => isSameEntry(item, oldItem)))
    .map(item => `[data-flip-id="card-${item.timestamp}"]`)
  const leavingSelectors = leaving.map(item => `[data-flip-id="card-${item.timestamp}"]`)

  ctx.value?.add(async () => {
    // get the height of each element
    const cardHeight = leavingSelectors[0] ? (document.querySelector(leavingSelectors[0]) as HTMLElement).offsetHeight : 0
    const cardGap = 8
    const leavingIndex = oldHistory.findIndex(item => isSameEntry(item, leaving[0]))

    const movingDuration = 0.5
    const movingStagger = 0.05
    const movingTotalDuration = movingDuration + Math.abs(movingStagger) * Math.max(movingSelectors.length - 1, 0)

    const flipState = Flip.getState(movingSelectors)
    const backgroundState = Flip.getState('[data-flip-id="history-bg"]')
    visibleHistory.value = [...newHistory]
    leavingEntry.value = leaving[0]
    await nextTick()
    
    const timeline = gsap.timeline({ onComplete: () => {
      visibleHistory.value = [...newHistory]
      leavingEntry.value = null
    } })
    
    timeline.add(Flip.from(backgroundState, {
      duration: movingTotalDuration,
      ease: 'power1.inOut',
    }), 0)
    timeline.add(Flip.from(flipState, {
      duration: movingDuration,
      stagger: movingStagger,
      ease: 'power1.inOut',
      absolute: true,
      targets: movingSelectors
    }), 0)
    timeline.set(leavingSelectors, {
      x: 0,
      y: leavingIndex * (cardHeight + cardGap)
    }, 0)
    timeline.to(leavingSelectors, {
      opacity: 0,
      x: 100,
      duration: 0.5,
      ease: 'power1.inOut'
    }, 0)
  })
}

onMounted(() => {
  ctx.value = gsap.context(() => {})
})

onBeforeUnmount(() => {
  ctx.value?.revert()
})
</script>