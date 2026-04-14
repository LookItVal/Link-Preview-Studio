<template>
  <a class="mx-(--xs-em) h-full aspect-square" href="https://github.com/lookitval" target="_blank" @mouseover="shimmer">
    <svg viewBox="0 0 10 10" class="h-full">   
      <defs>
        <mask id="github">
          <image href="/icons/github.svg" height="100%" width="100%" />
        </mask>
        <linearGradient id="linear-gradient" x1="0" y1="0.5" x2="1" y2="0.5">
          <stop offset="0%" style="stop-color:var(--color-red-500);stop-opacity:0" />
          <stop offset="100%" style="stop-color:var(--color-red-500);stop-opacity:1" />
        </linearGradient>
      </defs>
      <g mask="url(#github)">
        <rect width="100%" height="100%" fill="var(--color-lavender-100)" />
        <rect ref="shimmerBox" class='shimmer-box static' x="4.25" y="5" width="1.5" height="7" fill="url(#linear-gradient)" />
      </g>
    </svg>
  </a>
</template>

<script lang="ts" setup>
const isAnimating: Ref<boolean> = ref(false);
const shimmerBox: Ref<HTMLElement | null> = ref(null);

function shimmer(): void {
  if (isAnimating.value) return;
  isAnimating.value = true;
  shimmerBox.value!.classList.add('shimmer');
  shimmerBox.value!.classList.remove('static');
  setTimeout(() => {
    shimmerBox.value!.classList.remove('shimmer');
    shimmerBox.value!.classList.add('static');
    isAnimating.value = false;
  }, 1000);
}

defineExpose({
  shimmer
});
</script>

<style scoped>
svg {
  contain: layout, style, paint;
  will-change: auto;
}

.shimmer-box {
  will-change: width, transform;
  contain: style, paint;

  &.shimmer {
    animation: shimmer 1s linear;
    width: 1.5;
    transition: width 0.1s linear;
  }
  &.static {
    transform: rotate(5deg);
    transform-origin: center;
    width: 0;
    transition: width 1s linear;
  }
}
@keyframes shimmer {
  0% {
    transform: rotate(5deg);
    transform-origin: center;
  }
  100% {
    transform: rotate(-355deg);
    transform-origin: center;
  }
}
</style>