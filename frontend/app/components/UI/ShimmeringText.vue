<template>
  <div>
    <component
      :is="as"
      class="shimmer absolute text-transparent! z-1"
      :style="{
        background: `linear-gradient(110deg, transparent, transparent, ${backgroundColor}, transparent, transparent)`,
        animationDuration: speed + 's'
      }"
    >
      {{ text }}
    </component>
    <component
      :is="as"
      class="relative z-0"
      :style="{
        color: foregroundColor
      }"
    >
      {{ text }}
    </component>
  </div>
</template>

<script lang="ts" setup>
import { useConstants } from '@/composables/constants';

const { COLORS } = useConstants();
const allowedColors = Object.keys(COLORS) as (keyof typeof COLORS)[];

const props = withDefaults(defineProps<{
  text: string
  as: 'span' | 'p' | 'h1' | 'h2' | 'h3' | 'h4' | 'h5' | 'h6'
  speed?: number
  fgColor: typeof allowedColors[number]
  bgColor: typeof allowedColors[number]
}>(), {
  speed: 3
})

function validateColor(color: string) {
  return allowedColors.includes(color as typeof allowedColors[number])
}

const foregroundColor = computed(() =>
  validateColor(props.fgColor) ? `var(--color-${props.fgColor})` : 'inherit'
)
const backgroundColor = computed(() =>
  validateColor(props.bgColor) ? `var(--color-${props.bgColor})` : 'var(--purple-shimmer)'
)
</script>

<style scoped>
.shimmer {
  will-change: background-position;
  contain: layout style paint;
  background-size: 200% 100% !important;
  background-repeat: no-repeat !important;
  background-clip: text !important;
  animation-name: shimmer;
  animation-iteration-count: infinite;
  animation-timing-function: linear;
  animation-direction: reverse;
}
@keyframes shimmer {
  0% {
    background-position: 150% 0%;
  }
  100% {
    background-position: -50% 0%;
  }
}
</style>