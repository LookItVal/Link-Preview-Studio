<template>
  <a class="mx-(--xs-em) h-full aspect-square" href="https://www.instagram.com/lookitval" target="_blank" @mouseover="shimmer">
    <svg ref="instaSvg" class="h-full" viewBox="0 0 10 10">
      <defs>
        <mask id="instagram">
          <image href="/icons/instagram.svg" height="100%" width="100%" />
        </mask>
        <radialGradient id="radial-gradient" cx="50%" cy="50%" r="50%" fx="50%" fy="50%">
          <stop offset="0%" style="stop-color:var(--color-red-500);stop-opacity:1" />
          <stop offset="100%" style="stop-color:var(--color-red-500);stop-opacity:0" />
        </radialGradient>
      </defs>
      <g mask="url(#instagram)">
        <rect width="100%" height="100%" fill="var(--color-lavender-100)" />
        <path
id="insta-outer-path" d="M 2.5 7.5
                                       L 5 9.55
                                       C 9.25 9.55 9.55 9.25 9.55 5
                                       C 9.55 0.75 9.25 0.45 5 0.45
                                       C 0.75 0.45 0.45 0.75 0.45 5
                                       C 0.45 9.25 0.75 9.55 5 9.55
                                       L 7.5 7.5" stroke="none" fill="none"/>
        <path
id="insta-inner-path" d="M 5 5
                                       L 5 3
                                       C 3.75 3 3 3.75 3 5
                                       C 3 6.25 3.75 7 5 7
                                       C 6.25 7 7 6.25 7 5
                                       C 7 3.75 6.25 3 5 3
                                       Z" stroke="none" fill="none"/>
        <circle ref="outerShimmerBall" class='ball static' r="1" fill="url(#radial-gradient)">
          <animateMotion dur="1s" fill="freeze">
            <mpath href="#insta-outer-path" />
          </animateMotion>
        </circle>
        <circle ref="innerShimmerBall" class='ball static' r="1" fill="url(#radial-gradient)">
          <animateMotion dur="1s" fill="freeze">
            <mpath href="#insta-inner-path" />
          </animateMotion>
        </circle>
        <circle ref="highlightShimmerBall" class='ball static' cx="7.665" cy="2.325" r="0" fill="url(#radial-gradient)">
          <animate attributeName="r" dur="1s" values="0;0.75;0" keyTimes="0;0.5;1" fill="freeze" />
        </circle>
      </g>
    </svg>
  </a>
</template>


<script lang="ts" setup>
const isAnimating: Ref<boolean> = ref(false);
const instaSvg: Ref<HTMLElement | null> = ref(null);
const outerShimmerBall: Ref<HTMLElement | null> = ref(null);
const innerShimmerBall: Ref<HTMLElement | null> = ref(null);
const highlightShimmerBall: Ref<HTMLElement | null> = ref(null);

function shimmer(): void {
  if (isAnimating.value) return;
  isAnimating.value = true;
  const animations = instaSvg.value!.querySelectorAll('animateMotion, animate');
  animations.forEach(anim => {
    if (typeof (anim as SVGAnimationElement).beginElement === 'function') {
      const parent = anim.parentNode;
      if (parent) {
        parent.removeChild(anim);
        parent.appendChild(anim);
        (anim as SVGAnimationElement).beginElement();
      }
    }
  });
  outerShimmerBall.value!.classList.remove('static');
  outerShimmerBall.value!.classList.add('shimmer');
  innerShimmerBall.value!.classList.remove('static');
  innerShimmerBall.value!.classList.add('shimmer');
  highlightShimmerBall.value!.classList.remove('static');
  highlightShimmerBall.value!.classList.add('shimmer');
  setTimeout(() => {
    outerShimmerBall.value!.classList.add('static');
    outerShimmerBall.value!.classList.remove('shimmer');
    innerShimmerBall.value!.classList.add('static');
    innerShimmerBall.value!.classList.remove('shimmer');
    highlightShimmerBall.value!.classList.add('static');
    highlightShimmerBall.value!.classList.remove('shimmer');
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

.ball {
  will-change: opacity;
  contain: style, paint;

  &.shimmer {
    opacity: 1;
    transition: opacity 0.1s linear;
  }
  &.static {
    opacity: 0;
    transition: opacity 0.1s linear;
  }
}
.highlight-ball {
  will-change: r;
  contain: style, paint;

  .shimmer {
    animation: record-pulse 1s linear;
  }
}

@keyframes record-pulse {
  0% {
    r: 0;
  }
  50% {
    r: 0.75;
  }
  100% {
    r: 0;
  }
}
</style>