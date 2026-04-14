<template>
  <a class="mx-(--xs-em) h-full aspect-square" href="https://www.linkedin.com/in/qcecil" target="_blank" @mouseover="shimmer">
    <svg ref="linkedinSvg" class="h-full" viewBox="0 0 10 10">
      <defs>
        <mask id="linkedin">
          <image href="/icons/linkedin.svg" height="100%" width="100%" />
        </mask>
        <linearGradient id="linear-gradient" x1="0" y1="0.5" x2="1" y2="0.5">
          <stop offset="0%" style="stop-color:var(--color-red-500);stop-opacity:0" />
          <stop offset="100%" style="stop-color:var(--color-red-500);stop-opacity:1" />
        </linearGradient>
        <radialGradient id="radial-gradient" cx="50%" cy="50%" r="50%" fx="50%" fy="50%">
          <stop offset="0%" style="stop-color:var(--color-red-500);stop-opacity:1" />
          <stop offset="100%" style="stop-color:var(--color-red-500);stop-opacity:0" />
        </radialGradient>
      </defs>
      <g mask="url(#linkedin)">
        <rect width="100%" height="100%" fill="var(--color-lavender-100)" />
        <path
id="outer-path" d="M 6.25 8.25
                                 L 5 9.55
                                 C 9.25 9.55 9.55 9.25 9.55 5
                                 C 9.55 0.75 9.25 0.45 5 0.45
                                 C 0.75 0.45 0.45 0.75 0.45 5
                                 C 0.45 9.25 0.75 9.55 5 9.55
                                 Z" stroke="none" fill="none"/>
        <rect ref="shimmerBox" class='box static' x="1" y="1" width="1" height="7" fill="url(#linear-gradient)" />
        <circle ref="shimmerBall" class='ball static' r="1" fill="url(#radial-gradient)">
          <animateMotion dur="1s" fill="freeze">
            <mpath href="#outer-path" />
          </animateMotion>
        </circle>
        <rect ref="coverBox1" class="cover-box static" x="8.4" y="0" width="2" height="10" fill="var(--color-lavender-100)" />
        <rect ref="coverBox2" class="cover-box static" x="6" y="0" width="5" height="3" fill="var(--color-lavender-100)" />
        <rect ref="coverBox3" class="cover-box static" x="-7.05" y="0" width="2" height="10" fill="var(--color-lavender-100)" />
        <rect ref="coverBox4" class="cover-box static" x="-7.05" y="0" width="3" height="1.25" fill="var(--color-lavender-100)" />
      </g>
    </svg>
  </a>
</template>


<script lang="ts" setup>
const isAnimating: Ref<boolean> = ref(false);
const linkedinSvg: Ref<HTMLElement | null> = ref(null);
const shimmerBox: Ref<HTMLElement | null> = ref(null);
const shimmerBall: Ref<HTMLElement | null> = ref(null);
const coverBox1: Ref<HTMLElement | null> = ref(null);
const coverBox2: Ref<HTMLElement | null> = ref(null);
const coverBox3: Ref<HTMLElement | null> = ref(null);
const coverBox4: Ref<HTMLElement | null> = ref(null);

function shimmer(): void {
  if (isAnimating.value) return;
  isAnimating.value = true;
  const animations = linkedinSvg.value!.querySelectorAll('animateMotion');
  animations!.forEach(anim => {
    const parent = anim.parentNode;
    if (parent) {
      parent.removeChild(anim);
      parent.appendChild(anim);
      anim.beginElement();
    }
  });
  shimmerBox.value!.classList.remove('static');
  shimmerBox.value!.classList.add('shimmer');
  shimmerBall.value!.classList.remove('static');
  shimmerBall.value!.classList.add('shimmer');
  coverBox1.value!.classList.remove('static');
  coverBox1.value!.classList.add('shimmer');
  coverBox2.value!.classList.remove('static');
  coverBox2.value!.classList.add('shimmer');
  coverBox3.value!.classList.remove('static');
  coverBox3.value!.classList.add('shimmer');
  coverBox4.value!.classList.remove('static');
  coverBox4.value!.classList.add('shimmer');
  setTimeout(() => {
    shimmerBox.value!.classList.add('static');
    shimmerBox.value!.classList.remove('shimmer');
    shimmerBall.value!.classList.add('static');
    shimmerBall.value!.classList.remove('shimmer');
    coverBox1.value!.classList.add('static');
    coverBox1.value!.classList.remove('shimmer');
    coverBox2.value!.classList.add('static');
    coverBox2.value!.classList.remove('shimmer');
    coverBox3.value!.classList.add('static');
    coverBox3.value!.classList.remove('shimmer');
    coverBox4.value!.classList.add('static');
    coverBox4.value!.classList.remove('shimmer');
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

.box {
  will-change: width, transform;
  contain: style, paint;

  &.shimmer {
    width: 1.5;
    transform: translateX(7px);
    transition: width 0.1s linear, transform 1s linear;
  }
  &.static {
    width: 0;
    transform: translateX(0px);
    transition: width 0.1s linear, transform 0.3s cubic-bezier(.93,-0.32,1,-0.05);
  }
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
.cover-box {
  will-change: transform;
  contain: style, paint;

  &.shimmer {
    transform: translateX(0px);
    transition: transform 0.3s linear 0.5s;
  }
  &.static {
    transform: translateX(7px);
    transition: transform 0.1s linear 0.2s;
  }
}
</style>