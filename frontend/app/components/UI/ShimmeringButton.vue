<template>
  <div class="pointer">
    <AnimationsGlareHover 
      v-if="props.glare"
      class="shimmering-button relative overflow-hidden rounded-full z-1"
      :class="{ 'is-disabled': isDisabled }"
      style="width: max-content; height: 100%; border-radius: 50em; border-width: 0;"
    >
      <NuxtLink
        v-if="nuxtLinkUrl"
        :to="nuxtLinkUrl"
        class="relative overflow-hidden z-1"
        :class="isDisabled ? 'cursor-not-allowed pointer-events-none' : 'cursor-pointer'"
        :aria-disabled="isDisabled"
        style="text-decoration: none;"
      >
        <slot />
      </NuxtLink>
      <button
        v-else
        class="relative overflow-hidden z-1 px-(--xs-em)"
        :class="isDisabled ? 'cursor-not-allowed' : 'cursor-pointer'"
        :disabled="isDisabled"
        @click="handleClick"
      >
        <slot />
      </button>
    </AnimationsGlareHover>
    <div 
      v-else
      class="shimmering-button relative overflow-hidden rounded-full z-1"
      :class="{ 'is-disabled': isDisabled }"
      style="width: max-content; height: 100%; border-radius: 50em; border-width: 0;"
    >
      <NuxtLink
        v-if="nuxtLinkUrl"
        :to="nuxtLinkUrl"
        class="relative overflow-hidden z-1 px-(--xs-em)"
        :class="isDisabled ? 'cursor-not-allowed pointer-events-none' : 'cursor-pointer'"
        :aria-disabled="isDisabled"
        style="text-decoration: none;"
      >
        <slot />
      </NuxtLink>
      <button
        v-else
        class="relative overflow-hidden z-1 px-(--xs-em)"
        :class="isDisabled ? 'cursor-not-allowed' : 'cursor-pointer'"
        :disabled="isDisabled"
        @click="handleClick"
      >
        <slot />
      </button>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { useConstants } from '@/composables/constants';

const { COLORS: _COLORS } = useConstants();

const props = withDefaults(defineProps<{
  color1?: keyof typeof _COLORS,
  color2?: keyof typeof _COLORS,
  glare?: boolean
  speed?: number,
  disabled?: boolean,
  click?: (() => void) | string
}>(), {
  color1: 'lavender',
  color2: 'mauve',
  glare: true,
  speed: 3,
  disabled: false,
  click: () => {}
})

const isDisabled = computed(() => props.disabled)

const nuxtLinkUrl = computed(() => {
  if (typeof props.click === 'string') {
    if (props.click.startsWith('/')) {
      return props.click;
    }
  }
  return null;
});
const externalLinkUrl = computed(() => {
  if (typeof props.click === 'string') {
    if (props.click.startsWith('http')) {
      return props.click;
    }
  }
  return null;
});
const handleClick = () => {
  if (isDisabled.value) {
    return
  }

  if (typeof props.click === 'function') {
    props.click();
    return;
  }

  if (externalLinkUrl.value) {
    window.open(externalLinkUrl.value, '_blank', 'noopener,noreferrer');
  }
};

const conicGradient: Ref<string> = ref(`conic-gradient(from 90deg at 50% 50%, var(--color-${props.color1}), var(--color-${props.color2}), var(--color-${props.color1}))`);
</script>

<style scoped>
.shimmering-button {
  position: relative;
  overflow: hidden;
  border-radius: 50%;
  width: max-content;
  height: max-content;

  &::after {
    content: '';
    position: absolute;
    inset: 0;
    z-index: 2;
    border-radius: 50em;
    background: rgba(120, 120, 120, 0.42);
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.2s ease;
  }

  &::before {
    will-change: transform rotate;
    contain: layout style paint;

    content: '';
    position: absolute;
    width: 400%;
    aspect-ratio: 1 / 1;
    left: -150%;
    top: -175%;
    z-index: -1;
    border-radius: 50%;
    background: v-bind('conicGradient');
    animation-name: rotate;
    animation-duration: v-bind('props.speed + "s"');
    animation-play-state: v-bind('props.speed <= 0 ? "paused" : "running"');
    animation-timing-function: linear;
    animation-iteration-count: infinite;
    transform-origin: center center;
    pointer-events: none;
  }

  &.is-disabled {
    &::after {
      opacity: 1;
    }
  }
}

@keyframes rotate {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}
</style>