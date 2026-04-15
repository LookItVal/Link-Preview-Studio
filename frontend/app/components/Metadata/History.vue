<template>
  <div class="w-full max-w-xl space-y-2 pt-(--xs-em)" data-flip-id="history-bg">
    <MetadataHistoryCard
      v-for="entry in visibleHistory"
      :key="entry.timestamp"
      :ref="element => setCardRef(entry.timestamp, element)"
      :entry="entry"
      :data-flip-id="`card-${entry.timestamp}`"
    />
    <MetadataHistoryCard
      v-if="leavingEntry"
      :key="leavingEntry.entry.timestamp"
      :entry="leavingEntry.entry"
      :initial-active-preview="leavingEntry.activePreview"
      :data-flip-id="`card-${leavingEntry.entry.timestamp}`"
      class="absolute"
    />
  </div>
</template>

<script setup lang="ts">
import gsap from 'gsap'
import Flip from 'gsap/Flip'

const { history } = useHistory()
const visibleHistory = ref([...history.value])
type SocialPlatform = 'linkedin' | 'slack' | 'facebook' | 'twitter'

interface HistoryCardExpose {
  activePreview?: SocialPlatform | null | { value: SocialPlatform | null }
  calcMaxWidth?: () => void
}

interface LeavingEntryState {
  entry: any
  activePreview: SocialPlatform | null
}

const leavingEntry = ref<LeavingEntryState | null>(null)
const ctx = ref<gsap.Context | null>(null)
const cardRefs = ref<Map<number, unknown>>(new Map())

watch(() => history.value, async (newHistory, oldHistory) => {
  const newItems = newHistory.filter(
    newItem => !oldHistory.some(oldItem => isSameEntry(newItem, oldItem)),
  )
  const leaving = oldHistory.filter(
    item => !newHistory.some(newItem => isSameEntry(item, newItem)))
  if (leaving.length && newItems.length) {
    addHistoryWithOverflow(newHistory, oldHistory)
    return
  }
  if (newItems.length) {
    addHistoryEntry(newHistory, oldHistory)
  }
  if (leaving.length) {
    removeHistoryEntry(newHistory, oldHistory)
  }
})

function isSameEntry(a: any, b: any) {
  return a.timestamp === b.timestamp && a.url === b.url
}

function setCardRef(timestamp: number, element: unknown) {
  if (element) {
    cardRefs.value.set(timestamp, element as HistoryCardExpose)
    return
  }

  cardRefs.value.delete(timestamp)
}

function getCardSelector(entry: any) {
  return `[data-flip-id="card-${entry.timestamp}"]`
}

function recalcAllCardWidths() {
  cardRefs.value.forEach((ref) => {
    const cardRef = ref as HistoryCardExpose
    cardRef.calcMaxWidth?.()
  })
}

function getCardActivePreview(entry: any): SocialPlatform | null {
  const cardRef = cardRefs.value.get(entry.timestamp) as HistoryCardExpose | undefined
  const activePreview = cardRef?.activePreview

  if (activePreview && typeof activePreview === 'object' && 'value' in activePreview) {
    return activePreview.value ?? null
  }

  return activePreview ?? null
}

function calculateCardOffsetY(historySnapshot: any[], leavingItem: any) {
  const cardGap = 8
  const leavingIndex = historySnapshot.findIndex(item => isSameEntry(item, leavingItem))

  if (leavingIndex <= 0) {
    return 0
  }

  return historySnapshot.slice(0, leavingIndex).reduce((offset, item) => {
    const cardElement = document.querySelector(getCardSelector(item)) as HTMLElement | null
    const cardHeight = cardElement?.offsetHeight ?? 0

    return offset + cardHeight + cardGap
  }, 0)
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
      recalcAllCardWidths()
    } })
    const flipState = Flip.getState(movingSelectors)
    const backgroundState = Flip.getState('[data-flip-id="history-bg"]')
    visibleHistory.value = [...newItems, ...oldHistory]
    await nextTick()
    recalcAllCardWidths()

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
  const newItems = newHistory.filter(
    newItem => !oldHistory.some(oldItem => isSameEntry(newItem, oldItem)),
  )
  const movingItems = newHistory.filter(
    newItem => oldHistory.some(oldItem => isSameEntry(newItem, oldItem)),
  )
  const leavingItems = oldHistory.filter(
    item => !newHistory.some(newItem => isSameEntry(item, newItem))
  )
  if (!newItems.length || !leavingItems.length) {
    throw new Error('No history items to add or remove in overflow')
  }

  const leavingItem = leavingItems[0]
  const movingSelectors = movingItems
    .map(item => `[data-flip-id="card-${item.timestamp}"]`)
  const newItemsSelectors = newItems
    .map(item => `[data-flip-id="card-${item.timestamp}"]`)
  const leavingSelector = getCardSelector(leavingItem)

  ctx.value?.add(async () => {
    // Measure leaving position and capture states before DOM change
    const leavingOffsetY = calculateCardOffsetY(oldHistory, leavingItem) - 68
    const leavingActivePreview = getCardActivePreview(leavingItem)

    const movingDuration = 0.5
    const movingStagger = -0.05
    const movingTotalDuration = movingDuration + Math.abs(movingStagger) * Math.max(movingSelectors.length - 1, 0)

    const flipState = Flip.getState(movingSelectors)
    const backgroundState = Flip.getState('[data-flip-id="history-bg"]')

    // Update DOM: set final list + leaving clone
    visibleHistory.value = [...newHistory]
    leavingEntry.value = {
      entry: leavingItem,
      activePreview: leavingActivePreview,
    }
    await nextTick()
    recalcAllCardWidths()

    const timeline = gsap.timeline({ onComplete: () => {
      visibleHistory.value = [...newHistory]
      leavingEntry.value = null
      recalcAllCardWidths()
    } })

    // Background resize
    timeline.add(Flip.from(backgroundState, {
      duration: movingTotalDuration,
      ease: 'power2.out',
    }), 0)

    // Existing items move to new positions
    if (movingSelectors.length) {
      timeline.add(Flip.from(flipState, {
        duration: movingDuration,
        stagger: movingStagger,
        ease: 'power1.inOut',
        absolute: true,
        targets: movingSelectors
      }), 0)
    }

    // New items scale in
    timeline.from(newItemsSelectors, {
      scale: 0,
      duration: 0.5,
      ease: 'back.out(1.3)'
    }, '>-0.1')

    // Leaving clone: position then slide out
    timeline.set(leavingSelector, {
      x: 0,
      y: leavingOffsetY
    }, 0)
    timeline.to(leavingSelector, {
      opacity: 0,
      y: leavingOffsetY+100,
      duration: 0.5,
      ease: 'power1.inOut'
    }, 0)
  })
}


function removeHistoryEntry(newHistory: any[], oldHistory: any[]) {
  const leaving = oldHistory.filter(
    item => !newHistory.some(newItem => isSameEntry(item, newItem))
  )
  if (!leaving.length) {
    throw new Error('No history items to remove')
  }
  const leavingItem = leaving[0]
  const movingSelectors = newHistory
    .filter(item => oldHistory.some(oldItem => isSameEntry(item, oldItem)))
    .map(item => `[data-flip-id="card-${item.timestamp}"]`)
  const leavingSelector = getCardSelector(leavingItem)

  ctx.value?.add(async () => {
    const leavingOffsetY = calculateCardOffsetY(oldHistory, leavingItem)
    const leavingActivePreview = getCardActivePreview(leavingItem)

    const movingDuration = 0.5
    const movingStagger = 0.05
    const movingTotalDuration = movingDuration + Math.abs(movingStagger) * Math.max(movingSelectors.length - 1, 0)

    const flipState = Flip.getState(movingSelectors)
    const backgroundState = Flip.getState('[data-flip-id="history-bg"]')
    visibleHistory.value = [...newHistory]
    leavingEntry.value = {
      entry: leavingItem,
      activePreview: leavingActivePreview,
    }
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
    timeline.set(leavingSelector, {
      x: 0,
      y: leavingOffsetY
    }, 0)
    timeline.to(leavingSelector, {
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