<template>
  <NuxtLink to="https://lookitval.com" style="aspect-ratio: 1000 / 450;">
    <svg height="100%" viewBox="0 0 1000 450" xmlns="http://www.w3.org/2000/svg">
      <defs>
        <radialGradient id="logo-gradient" cx="20%" cy="0%" r="150%" fx="50%" fy="50%">
          <stop offset="0%" :style="`stop-color:var(--color-${primaryColor});stop-opacity:1`" />
          <stop offset="100%" :style="`stop-color:var(--color-${secondaryColor});stop-opacity:1`" />
        </radialGradient>
        <path 
          id="StartLargeKeyframe"
          d="M 499.999 0 A 225 225 0 1 0 500 0 Z"
        />
        <path 
          id="StartSmallKeyframe"
          d="M 499.99 200 A 25 25 0 1 0 500 200 Z"
        />
        <path
          id="QKeyframe1A"
          d="M 250 225 C 250 239 255 246 261 249 C 272 255 288 252 297 250 C 525 188 525 320 525 225 C 525 130 525 262 297 200 C 289 198 273 196 263 200 C 255 203 250 210 250 225 Z"
        />
        <path
          id="QKeyframe1B"
          d="M 139 224 C 139 252 155 262 175 262 C 178 262 180 262 183 262 C 227 260 229 238 230 223 C 232 193 210 182 184 182 C 182 182 179 182 177 182 C 156 184 139 192 139 224 Z"
        />
        <path
          id="QKeyframe1C"
          d="M 213 57 C 132 57 62 113 61 225 C 60 338 132 393 213 393 C 215 392 218 392 221 392 C 300 389 352 343 352 225 C 352 187 342 62 220 58 C 217 58 215 57 213 57 Z"
        />
        <path
          id="VKeyframe1A"
          d="M 499.99 200 A 25 25 0 1 0 500 200 Z"
        />
        <path
          id="VKeyframe1B"
          d="M 509 57 C 428 57 359 112 358 225 C 357 338 428 393 509 392 C 599 392 648 341 648 225 C 648 119 596 57 509 57 Z"
        />
        <path
          id="CKeyframe1A"
          d="M 750 225 C 750 243 742 249 733 251 C 723 253 710 252 703 250 C 487 200 473 316 474 225 C 475 134 490 250 703 200 C 711 198 724 197 734 199 C 743 201 750 208 750 225 Z"
        />
        <path
          id="CKeyframe1B"
          d="M 870 224 C 870 256 851 264 829 264 C 828 264 825 264 821 264 C 804 262 770 255 770 223 C 770 220 771 184 822 180 C 824 180 827 180 830 180 C 852 180 870 191 870 224 Z"
        />
        <path
          id="CKeyframe1C"
          d="M 805 57 C 801 57 797 57 793 57 C 709 63 655 128 654 225 C 653 325 712 389 798 392 C 801 392 803 392 805 392 C 903 392 945 330 945 225 C 945 185 934 57 805 57 Z"
        />
        <path
          id="QKeyframe2A"
          d="M 213 57 C 132 57 62 113 61 225 C 60 338 132 393 213 393 C 280 392 325 363 343 300 C 349 279 352 254 352 225 C 352 203 350 182 345 164 C 328 96 281 57 213 57 Z M 212 206 C 212 206 212 206 212 206 C 212 206 212 206 212 206 C 212 206 212 206 212 206 C 212 206 212 206 212 206 Z"
        />
        <path
          id="QKeyframe2B"
          d="M 205 56 C 110 57 60 142 61 225 C 63 334 133 391 209 393 C 222 393 242 393 255 387 C 260 393 263 397 268 404 C 287 429 315 414 307 400 C 303 394 293 380 287 370 C 341 338 357 256 357 225 C 358 160 332 56 205 56 Z M 253 208 C 253 230 229 257 207 256 C 185 256 163 230 163 208 C 163 187 185 162 207 161 C 229 161 253 186 253 208 Z"
        />
        <path
          id="VKeyframe2A"
          d="M 430 360 C 360 290 360 250 360 90 C 359 87 358 84 356 82 C 355 79 354 77 352 74 C 349 67 345 61 341 56 C 337 51 334 47 330 44 C 326 40 323 35 321 29 C 317 17 320 6 336 11 C 340 12 345 14 351 17 C 357 21 363 27 369 34 C 374 40 379 45 384 50 C 386 53 389 56 392 59 C 395 61 397 63 400 65 C 430 85 470 110 500 110 C 530 110 541 106 560 100 C 590 90 629 70 634 130 C 640 190 650 280 620 350 C 616 359 616 371 618 383 C 619 390 622 397 625 404 C 627 408 629 411 632 414 C 635 418 639 420 644 420 C 647 420 650 420 652 421 C 661 422 661 427 653 433 C 649 435 644 438 636 440 C 633 441 630 442 626 443 C 619 445 612 446 603 447 C 591 449 576 450 560 450 C 525 450 467 397 430 360 Z"
        />
        <path
          id="VKeyframe2B"
          d="M 480 390 C 437 333 388 357 360 90 C 345 55 330 30 285 30 H 85 C 77 30 70 32 64 34 C 58 37 53 40 48 44 C 41 51 35 54 30 55 C 21 58 17 52 17 45 C 17 38 19 30 25 25 C 32 18 40 12 50 8 C 60 3 72 0 85 0 H 285 C 314 0 335 6 351 17 C 365 28 376 44 385 65 C 385 65 470 194 500 194 C 530 194 596 100 596 100 C 611 70 629 70 634 130 C 640 190 563 310 599 390 C 606 406 624 420 644 420 H 915 C 923 420 931 418 937 416 C 943 413 949 409 953 404 C 958 399 964 395 969 393 C 983 386 994 391 989 405 C 987 411 982 418 974 426 C 968 433 960 438 951 442 C 940 447 928 450 915 450 H 560 C 525 450 500 425 480 390 Z"
        />
        <path
          id="VKeyframe2C"
          d="M 480 390 L 360 90 C 345 55 330 30 285 30 H 85 C 55 30 30 55 30 85 V 365 C 30 380 36 394 46 404 C 52 408 55 415 54 421 C 53 426 49 431 42 432 C 37 432 30 429 22 422 C 8 407 0 387 0 365 V 85 C 0 62 10 40 25 25 C 40 10 62 0 85 0 H 285 C 340 0 365 20 385 65 C 385 65 470 310 500 310 C 530 310 605 80 605 80 C 620 50 646 65 640 80 C 640 80 556 295 599 390 C 606 406 624 420 644 420 H 915 C 945 420 970 395 970 365 V 85 C 970 71 965 58 956 49 C 951 44 947 39 944 35 C 935 20 941 12 953 13 C 960 14 969 19 978 28 C 992 43 1000 63 1000 85 V 365 C 1000 412 962 450 915 450 H 560 C 525 450 500 425 480 390 Z"
        />
        <path
          id="CKeyframe2A"
          d="M 805 57 C 738 57 678 95 660 173 C 656 189 654 206 654 225 C 654 245 656 262 660 278 C 678 355 739 392 805 392 C 852 392 888 378 912 348 C 916 342 921 336 924 328 C 928 320 932 311 935 302 C 937 294 939 286 941 278 C 942 269 943 259 944 249 C 944 241 945 233 945 225 C 945 219 945 213 944 208 C 944 203 943 197 943 192 C 942 184 941 177 939 169 C 937 161 935 153 932 145 C 930 138 927 132 924 126 C 901 81 860 57 805 57 Z"
        />
        <path
          id="CKeyframe2B"
          d="M 800 57 C 720 58 655 112 654 225 C 653 338 726 392 803 392 C 903 394 935 326 939 283 C 945 225 900 220 893 252 C 890 275 874 290 842 290 C 808 290 786 267 785 224 C 784 188 806 158 840 158 C 867 159 886 167 893 197 C 900 224 950 220 939 168 V 167 C 924 89 869 56 800 57 Z"
        />
        <mask id="logo-mask" height="100%" width="100%">
          <path 
            id="QFinal" 
            d="M 205 56 C 110 57 60 142 61 225 C 63 334 133 391 209 393 C 222 393 242 393 255 387 C 272 409 286 428 302 450 H 498 C 476 450 461 419 461 419 C 461 419 332 419 321 419 C 307 402 300 387 287 370 C 341 338 357 256 357 225 C 358 160 332 56 205 56 Z M 325 225 C 322 301 277 337 272 345 L 221 278 L 185 278 L 242 358 C 231 362 218 362 207 362 C 192 361 92 344 90 225 C 89 147 147 85 205 85 C 270 86 328 129 325 225 Z"
            fill="white"
          />
          <path
            id="VFinal" 
            d="M 480 390
               L 360 90
               C 345 55 330 30 285 30
               H 85
               A 55 55 0 0 0 30 85
               V 365
               A 55 55 0 0 0 85 420
               H 460
               S 475 450 500 450
               H 85
               A 85 85 0 0 1 0 365
               V 85
               A 85 85 0 0 1 85 0
               H 285
               C 340 0 365 20 385 65
               L 500 350
               L 615 55
               H 650
               L 515 390
               C 525 410 540 420 560 420
               H 915
               A 55 55 0 0 0 970 365
               V 85
               A 55 55 0 0 0 915 30
               H 400
               C 395 20 385 0 365 0
               H 915
               A 85 85 0 0 1 1000 85
               V 365
               A 85 85 0 0 1 915 450
               H 560
               C 525 450 500 425 480 390
               Z"
            fill="white"
          />
          <path
            id="CFinal"
            d="M 800 57 C 720 58 655 112 654 225 C 653 338 726 392 803 392 C 903 394 935 326 939 283 L 908 282 C 903 330 869 361 804 360 C 733 360 688 312 686 225 C 684 149 729 87 800 88 C 856 90 895 107 908 168 H 939 V 167 C 924 89 869 56 800 57 Z"
            fill="white"
          />
        </mask>
      </defs>
      <g mask="url(#logo-mask)">
        <rect width="100%" height="100%" fill="url(#logo-gradient)" />
        <circle r="100" cx="1100" cy="450" fill="white"/>
      </g>
    </svg>
  </NuxtLink>
</template>

<script lang="ts" setup>
import { gsap } from 'gsap';
import { useConstants } from '@/composables/constants';

const { COLORS: _COLORS } = useConstants();

const props = withDefaults(defineProps<{
  startPosition?: 'final' | 'middle' | 'start',
  animateOnMount?: boolean,
  duration?: number,
  easeFunction?: string,
  delay?: number,
  holdAtMiddle?: number,
  primaryColor?: keyof typeof _COLORS,
  secondaryColor?: keyof typeof _COLORS
}>(), {
  startPosition: 'final',
  animateOnMount: true,
  duration: 1.5,
  easeFunction: "standard",
  delay: 0,
  holdAtMiddle: 0,
  primaryColor: 'lavender',
  secondaryColor: 'mauve'
});

function toPosition(position: 'final' | 'middle' | 'start') {
  switch (position) {
    case 'final':
      gsap.set("#logo-mask #QFinal", { morphSVG: "#QFinal" });
      gsap.set("#logo-mask #VFinal", { morphSVG: "#VFinal" });
      gsap.set("#logo-mask #CFinal", { morphSVG: "#CFinal" });
      break;
    case 'middle':
      gsap.set("#logo-mask #QFinal", { morphSVG: "#QKeyframe2A" });
      gsap.set("#logo-mask #VFinal", { morphSVG: "#VKeyframe1B" });
      gsap.set("#logo-mask #CFinal", { morphSVG: "#CKeyframe2A" });
      break;
    case 'start':
      gsap.set("#logo-mask #QFinal", { morphSVG: "#StartSmallKeyframe" });
      gsap.set("#logo-mask #VFinal", { morphSVG: "#StartLargeKeyframe" });
      gsap.set("#logo-mask #CFinal", { morphSVG: "#StartSmallKeyframe" });
      break;
  }
}

function animateToMiddle({paused = false, duration = props.duration, easeFunction = props.easeFunction, delay = props.delay} = {}): gsap.core.Timeline {
  easeFunction = easeFunction === 'standard' ? 'power1.inOut' : easeFunction;

  const timeline = gsap.timeline({ paused });

  const QKeyframe1Timeline = gsap.timeline( {paused: true} );
  QKeyframe1Timeline.to("#logo-mask #QFinal", {
    duration: 1,
    ease: 'power1.in',
    morphSVG: "#QKeyframe1A"
  })
  .to("#logo-mask #QFinal", {
    duration: 1,
    ease: 'back.out',
    morphSVG: "#QKeyframe1B"
  })
  .to("#logo-mask #QFinal", {
    duration: 2,
    ease: 'back.inOut',
    morphSVG: "#QKeyframe1C"
  });

  const VKeyframe1Timeline = gsap.timeline( {paused: true} );
  VKeyframe1Timeline.to("#logo-mask #VFinal", {
    duration: 2,
    ease: 'power2.inOut',
    morphSVG: "#VKeyframe1A"
  })
  .to("#logo-mask #VFinal", {
    duration: 3,
    ease: 'power1.inOut',
    morphSVG: "#VKeyframe1B"
  });

  const CKeyframe1Timeline = gsap.timeline( {paused: true} );
  CKeyframe1Timeline.to("#logo-mask #CFinal", {
    duration: 1,
    ease: 'power1.in',
    morphSVG: "#CKeyframe1A"
  })
  .to("#logo-mask #CFinal", {
    duration: 1,
    ease: 'back.out',
    morphSVG: "#CKeyframe1B"
  })
  .to("#logo-mask #CFinal", {
    duration: 2,
    ease: 'back.inOut',
    morphSVG: "#CKeyframe1C"
  });

  timeline.call(toPosition, ['start'], delay);
  timeline.to(QKeyframe1Timeline, {
    progress: 1,
    duration,
    ease: easeFunction
  }, delay)
  .to(VKeyframe1Timeline, {
    progress: 1,
    duration,
    ease: easeFunction
  }, `<`)
  .to(CKeyframe1Timeline, {
    progress: 1,
    duration,
    ease: easeFunction
  }, `<`);

  return timeline;
}

function animateToFinal({paused = false, duration = props.duration, easeFunction = props.easeFunction, delay = props.holdAtMiddle} = {}): gsap.core.Timeline {
  easeFunction = easeFunction === 'standard' ? 'power2.inOut' : easeFunction;

  const timeline = gsap.timeline({ paused });

  const QKeyframe2Timeline = gsap.timeline( {paused: true} );
  QKeyframe2Timeline.to("#logo-mask #QFinal", {
    duration: 1,
    ease: 'linear',
    morphSVG: "#QKeyframe2B"
  })
  .to("#logo-mask #QFinal", {
    duration: 1,
    ease: 'linear',
    morphSVG: "#QFinal"
  });

  const VKeyframe2Timeline = gsap.timeline( {paused: true} );
  VKeyframe2Timeline.to("#logo-mask #VFinal", {
    duration: 2,
    ease: 'power1.in',
    morphSVG: "#VKeyframe2A"
  })
  .to("#logo-mask #VFinal", {
    duration: 1,
    ease: 'linear',
    morphSVG: "#VKeyframe2B"
  })
  .to("#logo-mask #VFinal", {
    duration: 1,
    ease: 'linear',
    morphSVG: "#VKeyframe2C"
  })
  .to("#logo-mask #VFinal", {
    duration: 1,
    ease: 'linear',
    morphSVG: "#VFinal"
  });

  const CKeyframe2Timeline = gsap.timeline( {paused: true} );
  CKeyframe2Timeline.to("#logo-mask #CFinal", {
    duration: 1,
    ease: 'linear',
    morphSVG: "#CKeyframe2B"
  })
  .to("#logo-mask #CFinal", {
    duration: 1,
    ease: 'linear',
    morphSVG: "#CFinal"
  });

  timeline.call(toPosition, ['middle'], delay);
  timeline.to(QKeyframe2Timeline, {
    progress: 1,
    duration,
    ease: easeFunction
  }, delay)
  .to(VKeyframe2Timeline, {
    progress: 1,
    duration,
    ease: easeFunction
  }, '<')
  .to(CKeyframe2Timeline, {
    progress: 1,
    duration,
    ease: easeFunction
  }, '<');

  return timeline;
}

function animateEntrance({ paused = false } = {}) {
  const timeline = gsap.timeline({ paused });
  timeline.add(animateToMiddle());
  timeline.add(animateToFinal());
  timeline.call(toPosition, ['final']);
  return timeline;
}

onMounted(() => {
  if (props.animateOnMount) {
    const ctx = gsap.context(() => {
      const timeline = gsap.timeline({ onComplete: () => {
        ctx.revert();
      } });
      timeline.add(animateEntrance());
    });
  } else {
    toPosition(props.startPosition!);
  }
});

defineExpose({
  animateEntrance,
  animateToMiddle,
  animateToFinal,
  toPosition
});
</script>