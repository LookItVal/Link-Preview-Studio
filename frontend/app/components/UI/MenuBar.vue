<template>
  <nav ref="menuContainer" class="w-full flex flex-row justify-center items-center">
    <div
      ref="menuBar"
      class="h-(--l-em) p-(--xxs-em) flex flex-row justify-between bg-(--color-surface-300) md:text-3xl text-xl w-content rounded-[20px]"
    >
      <UILogo
        ref="logo"
        :animate-on-mount="false"
        startPosition="final"
        :primary-color="props.primaryColor"
        :secondary-color="props.secondaryColor"
      />
    </div>
  </nav>
</template>

<script setup lang="ts">
import { gsap } from 'gsap';
import type Logo from '@/components/UI/Logo.vue';

const { COLORS: _COLORS } = useConstants();

const props = withDefaults(defineProps<{
  primaryColor?: keyof typeof _COLORS,
  secondaryColor?: keyof typeof _COLORS,
  startPosition?: 'start' | 'first' | 'second' | 'final',
  animateOnMount?: boolean,
  duration?: number,
  initialDelay?: number,
  delayBetweenItems?: number,
}>(), {
  primaryColor: 'lavender',
  secondaryColor: 'mauve',
  startPosition: 'final',
  animateOnMount: false,
  duration: 1,
  initialDelay: 0,
  delayBetweenItems: 0,
});

const loaded = ref(false);
const menuBar = ref<HTMLElement | null>(null);
const menuContainer = ref<HTMLElement | null>(null);
const logo = ref<InstanceType<typeof Logo> | null>(null);

const barWidth = ref(0);
const barHeight = ref(0);
const barRadius = ref('0px');

function toPosition(position: 'start' | 'first' | 'second' | 'final') {
  switch (position) {
    case 'start':
      toPosition('first');
      gsap.set(menuBar.value, {
        scale: 0
      });
      break;

    case 'first':
      toPosition('second');
      logo.value?.toPosition('start');
      gsap.set(menuBar.value, {
        width: barHeight.value,
        scale: 1
      });
      gsap.set(logo.value!.$el.querySelectorAll('svg'), {
        width: logo.value!.$el.clientHeight * (1000/450) || 0,
        x: ((logo.value!.$el.clientHeight * (1000/450)) - logo.value!.$el.clientHeight) / (-2) || 0
      });
      break;

    case 'second':
      toPosition('final');
      logo.value?.toPosition('middle');
      gsap.set(menuBar.value, {
        borderRadius: '40px',
        width: barWidth.value
      });
      gsap.set(logo.value!.$el, {
        width: logo.value!.$el.clientHeight || 0,
      });
      gsap.set(logo.value!.$el.querySelectorAll('svg'), {
        width: logo.value!.$el.clientHeight * (1000/450) || 0,
        x: ((logo.value!.$el.clientHeight * (1000/450)) - logo.value!.$el.clientHeight) / (-2) || 0
      });
      gsap.set(logo.value!.$el.querySelectorAll('svg'), {
        width: logo.value!.$el.clientHeight * (1000/450) || 0,
        x: 0
      });
      break;    
    case 'final':
      logo.value?.toPosition('final');
      gsap.set(menuBar.value, {
        borderBottomLeftRadius: barRadius.value,
        borderTopRightRadius: barRadius.value,
        borderBottomRightRadius: barRadius.value,
        borderTopLeftRadius: barRadius.value,
      });
      break;
  }
}


function animateToFirstPosition({paused = false, duration = props.duration, easeFunction = 'elastic', delay = props.initialDelay} = {}): gsap.core.Timeline {
  const timeline = gsap.timeline({ paused, delay });
  timeline.call(() => toPosition('start'));
  timeline.to(menuBar.value, {
    scale: 1,
    duration,
    ease: easeFunction
  });
  return timeline;
}

function animateToSecondPosition({paused = false, duration = props.duration, easeFunction = 'power2.inOut', delay = props.delayBetweenItems} = {}): gsap.core.Timeline {
  const timeline = gsap.timeline({ paused, delay });
  timeline.call(() => {
    toPosition('first');
  });
  timeline.to(menuBar.value, {
    width: barWidth.value,
    duration: duration * 2 / 3,
    ease: easeFunction
  });
  timeline.add(logo.value!.animateToMiddle({ duration }), 0);
  timeline.to(logo.value!.$el.querySelectorAll('svg'), {
    width: logo.value!.$el.clientHeight * (1000/450) || 0,
    x: 0,
    duration: duration * 2 / 3,
    ease: easeFunction
  }, 0);
  return timeline;
}

function animateToFinalPosition({paused = false, duration = props.duration, easeFunction = 'power1.inOut', delay = props.delayBetweenItems} = {}): gsap.core.Timeline {
  const timeline = gsap.timeline({ paused, delay });
  timeline.call(() => {
    toPosition('second');
  });
  timeline.to(menuBar.value, {
    borderRadius: barRadius.value,
    duration: duration,
    ease: easeFunction
  }, 0);
  timeline.add(logo.value!.animateToFinal({ duration }), 0);
  return timeline;
}


function animateEntrance({paused = false, initialDelay = props.initialDelay} = {}) {
  const timeline = gsap.timeline({ paused });
  timeline.add(animateToFirstPosition({ delay: initialDelay }));
  timeline.add(animateToSecondPosition());
  timeline.add(animateToFinalPosition());
  return timeline;
}


onMounted(async () => {
  loaded.value = true;
  if (menuBar.value) {
    barWidth.value = menuBar.value.clientWidth;
    barHeight.value = menuBar.value.clientHeight;
    barRadius.value = getComputedStyle(menuBar.value).borderTopLeftRadius || '0px';

    if (props.animateOnMount) {
      const ctx = gsap.context(() => {
        toPosition('start');
        const timeline = gsap.timeline({ onComplete: () => {} });
        timeline.add(animateEntrance());
      });
    } else {
      toPosition(props.startPosition);
    }
  }
});
</script>