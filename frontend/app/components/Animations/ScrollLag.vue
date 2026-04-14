<template>
  <div ref='content' class="will-change-transform" :style="`transform: translateY(${yOffset}px);`">
    <slot />
  </div>
</template>

<script lang="ts" setup>
import { useSmoothScroller } from '@/composables/smoothScroller';
const { currentVelocity } = useSmoothScroller();

const props = withDefaults(defineProps<{
  active?: boolean
  reverse?: boolean
}>(), {
  active: true,
  reverse: false
});

const yOffset = computed(() => {
  if (!props.active) return 0;
  const clampedVelocity = Math.max(-100, Math.min(100, currentVelocity.value / 40));
  return props.reverse ? -clampedVelocity : clampedVelocity;
});
</script>