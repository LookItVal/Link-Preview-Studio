<template>
  <nav ref="menuContainer" class="w-full flex flex-row justify-center">
    <AnimationsScrollLag v-if="loaded" :active="!!props.scrollLag" :reverse="props.scrollLag === 'reverse'">
      <div
        ref="menuBar"
        :class="[
          'h-(--l-em) p-(--xxs-em) flex flex-row justify-between bg-(--color-surface-300) md:text-3xl text-xl',
          props.type === 'micro' ? 'md:w-sm w-xs' : props.type === 'logo' ? 'w-content' : 'w-4xl'
        ]"
        :style="props.type === 'logo' ? 'border-radius: 20em 20em 20em 20em;' : 'border-radius: 20em 50em 50em 20em;'"
      >
        <UILogo
          ref="logo"
          :animate-on-mount="false"
          start-position="final"
          :primary-color="props.primaryColor"
          :secondary-color="props.secondaryColor"
        />
        <Socials
          v-if="props.type === 'simple'"
          ref="socialsSection"
          class="py-(--xxs-em) rounded-full bg-(--color-base-100)"
        />
        <UIShimmeringButton
          v-if="props.type !== 'logo'"
          ref="featuredButton"
          :color1="props.secondaryColor"
          :color2="props.primaryColor"
          :speed="30"
          :click="props.featuredAction"
       >
          <p ref="featuredButtonText" class="text-base-100 font-black px-(--s-em) text-nowrap">{{ props.featuredText }}</p>
        </UIShimmeringButton>
      </div>
    </AnimationsScrollLag>
  </nav>
</template>

<script lang="ts" setup>
import { gsap } from 'gsap';
import { useConstants } from '@/composables/constants';
import type Logo from '@/components/UI/Logo.vue';
import type Socials from '@/components/Socials/Socials.vue';
import type ShimmeringButton from '@/components/UI/ShimmeringButton.vue';

const { COLORS: _COLORS } = useConstants();

const props = withDefaults(defineProps<{
  type?: 'logo' | 'micro' | 'simple' | 'full',
  featuredText?: string,
  featuredAction?: (() => void) | string,
  primaryColor?: keyof typeof _COLORS,
  secondaryColor?: keyof typeof _COLORS,
  startPosition?: 'start' | 'final',
  animateOnMount?: boolean,
  duration?: number,
  initialDelay?: number,
  delayBetweenItems?: number,
  pin?: boolean,
  scrollLag?: boolean | 'reverse' 
}>(), {
  type: 'micro',
  featuredText: 'Featured',
  featuredAction: () => {},
  primaryColor: 'lavender',
  secondaryColor: 'mauve',
  startPosition: 'final',
  animateOnMount: false,
  duration: 1,
  initialDelay: 0,
  delayBetweenItems: 0,
  pin: false,
  scrollLag: false
});

const loaded = ref(false);

const menuBar = ref<HTMLElement | null>(null);
const menuContainer = ref<HTMLElement | null>(null);
const logo = ref<InstanceType<typeof Logo> | null>(null);
const socialsSection = ref<InstanceType<typeof Socials> | null>(null);
const featuredButton = ref<InstanceType<typeof ShimmeringButton> | null>(null);
const featuredButtonText = ref<HTMLElement | null>(null);

const barWidth = ref(0);
const barHeight = ref(0);
const barTopLeftRadius = ref('0px');
const barTopRightRadius = ref('0px');
const buttonWidth = ref(0);
const buttonHeight = ref(0);

function toPosition(position: 'start' | 'first' | 'second' | 'third' | 'final') {
  switch (position) {
    case 'start':
      toPosition('first');
      gsap.set(menuBar.value, {
        scale: 0
      });
      break;

    case 'first':
      toPosition('second');
      gsap.set(menuBar.value, {
        width: barHeight.value,
        scale: 1
      });
      gsap.set(featuredButton.value?.$el, {
        xPercent: -100
      });
      break;

    case 'second':
      toPosition('third');
      logo.value?.toPosition('start');
      gsap.set(featuredButton.value?.$el, {
        xPercent: 0
      });
      gsap.set(featuredButton.value?.$el.querySelectorAll('.shimmering-button'), {
        width : buttonHeight.value,
      });
      gsap.set(menuBar.value, {
        width: barWidth.value
      });
      gsap.set(logo.value!.$el, {
        width: logo.value!.$el.clientHeight || 0,
      });
      gsap.set(logo.value!.$el.querySelectorAll('svg'), {
        width: logo.value!.$el.clientHeight * (1000/450) || 0,
        x: ((logo.value!.$el.clientHeight * (1000/450)) - logo.value!.$el.clientHeight) / (-2) || 0
      });
      break;

    case 'third':
      toPosition('final');
      logo.value?.toPosition('middle');
      gsap.set(featuredButton.value?.$el.querySelectorAll('.shimmering-button'), {
        width : buttonWidth.value,
      });
      gsap.set(menuBar.value, {
        borderBottomLeftRadius: barTopRightRadius.value,
        borderTopRightRadius: barTopRightRadius.value,
        borderBottomRightRadius: barTopRightRadius.value,
        borderTopLeftRadius: barTopRightRadius.value,
      });
      gsap.set(featuredButtonText.value, { text: '' });
      gsap.set(logo.value!.$el.querySelectorAll('svg'), {
        width: logo.value!.$el.clientHeight * (1000/450) || 0,
        x: 0
      });
      break;
      
    case 'final':
      logo.value?.toPosition('final');
      gsap.set(menuBar.value, {
        borderBottomLeftRadius: barTopLeftRadius.value,
        borderTopRightRadius: barTopRightRadius.value,
        borderBottomRightRadius: barTopRightRadius.value,
        borderTopLeftRadius: barTopLeftRadius.value,
      });
      gsap.set(featuredButtonText.value, { text: props.featuredText });
      break;
  }
}

function animateToFirstPosition({paused = false, duration = props.duration, easeFunction = 'elastic', delay = props.initialDelay} = {}): gsap.core.Timeline {
  const timeline = gsap.timeline({ paused, delay });
  timeline.call(() => {
    toPosition('start');
  });

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

  const subTimeline = gsap.timeline({ paused: true });
  subTimeline.to(featuredButton.value?.$el, {
    xPercent: 0,
    duration: 1,
    ease: 'linear'
  }, 0).to(menuBar.value, {
    width: (buttonHeight.value * 2) + (parseFloat(getComputedStyle(menuBar.value!).paddingLeft) * 2),
    duration: 1,
    ease: 'linear'
  }, 0)
  .to(menuBar.value, {
    width: barWidth.value,
    duration: 3,
    ease: 'linear'
  });

  timeline.to(subTimeline, {
    progress: 1,
    duration,
    ease: easeFunction
  });

  return timeline;
}

function animateToThirdPosition({paused = false, duration = props.duration, easeFunction = 'power3.inOut', delay = props.delayBetweenItems} = {}): gsap.core.Timeline {
  const timeline = gsap.timeline({ paused, delay });
  timeline.call(() => {
    toPosition('second');
  });

  const subTimeline = gsap.timeline({ paused: true });
  subTimeline.to(logo.value!.$el.querySelectorAll('svg'), {
    x: 0,
    ease: 'power4.out',
    duration
  });
  subTimeline.add(logo.value!.animateToMiddle({ duration }), 0)

  timeline.to(subTimeline, {
    progress: 1,
    duration,
    ease: 'linear'
  })
  .to(featuredButton.value?.$el.querySelectorAll('.shimmering-button'), {
    width : buttonWidth.value,
    duration,
    ease: easeFunction
  }, `<`)

  return timeline;
}

function animateToFinalPosition({paused = false, duration = props.duration, easeFunction = 'power1.inOut', delay = props.delayBetweenItems} = {}): gsap.core.Timeline {
  const timeline = gsap.timeline({ paused, delay });
  timeline.call(() => {
    toPosition('third');
  });

  timeline.add(logo.value!.animateToFinal({ duration }), 0)
  timeline.to(featuredButtonText.value, {
    text: props.featuredText,
    duration,
    ease: easeFunction
  }, `<`)
  timeline.to(menuBar.value, {
    borderBottomLeftRadius: barTopLeftRadius.value,
    borderTopLeftRadius: barTopLeftRadius.value,
    duration,
    ease: easeFunction
  }, `<`)

  return timeline;
}

function animateEntrance({paused = false, initialDelay = props.initialDelay} = {}) {
  const timeline = gsap.timeline({ paused });
  timeline.add(animateToFirstPosition( { delay: initialDelay } ));
  timeline.add(animateToSecondPosition());
  timeline.add(animateToThirdPosition());
  timeline.add(animateToFinalPosition());
  timeline.call(toPosition, ['final']);
  return timeline;
}

onMounted(async () => {
  loaded.value = true;

  await nextTick();
  await nextTick();

  if (menuBar.value) {
    // Initialize reactive values after DOM is mounted
    barWidth.value = menuBar.value.offsetWidth || 0;
    barHeight.value = menuBar.value?.offsetHeight || 0;
    barTopLeftRadius.value = getComputedStyle(menuBar.value || document.documentElement).borderTopLeftRadius || '0px';
    barTopRightRadius.value = getComputedStyle(menuBar.value || document.documentElement).borderTopRightRadius || '0px';
    buttonHeight.value = featuredButton.value?.$el.querySelectorAll('.shimmering-button')[0]?.clientHeight || 0;
    buttonWidth.value = featuredButton.value?.$el.querySelectorAll('.shimmering-button')[0]?.clientWidth || 0;

    if (props.animateOnMount) {
      const ctx = gsap.context(() => {
        toPosition('start');
        const timeline = gsap.timeline({ onComplete: () => { ctx.revert(); } });
        timeline.add(animateEntrance());
      });
    } else {
      toPosition(props.startPosition!);
    }
    if (props.pin) {
      gsap.to(menuContainer.value, {
        scrollTrigger: {
          trigger: menuContainer.value,
          start: 'top top',
          end: () => document.body.scrollHeight,
          pin: true,
          pinType: 'transform'
        }
      });
    }
  }
});

defineExpose({
  animateEntrance,
  animateToFirstPosition,
  animateToSecondPosition,
  animateToThirdPosition,
  animateToFinalPosition,
  toPosition
});
</script>