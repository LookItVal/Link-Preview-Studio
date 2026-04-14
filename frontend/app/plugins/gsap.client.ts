import { gsap } from 'gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'
import { ScrollSmoother } from 'gsap/ScrollSmoother'
import { Flip } from "gsap/Flip";
import { MorphSVGPlugin } from 'gsap/MorphSVGPlugin';
import { MotionPathPlugin } from 'gsap/MotionPathPlugin';


export default defineNuxtPlugin(() => {
  gsap.registerPlugin(ScrollTrigger, ScrollSmoother, Flip, MorphSVGPlugin, MotionPathPlugin);
})