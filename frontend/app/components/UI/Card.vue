<template>
  <div class="ui-card">
    <div
      ref="background"
      class="card-background"
      :style="{
        borderRadius: borderRadius + 'rem',
        backgroundColor: backgroundColor,
        opacity: opacity
      }"
    />
    <div ref="content" class="contents">
      <slot />
    </div>
  </div>
</template>

<script lang="ts" setup>
const content = ref<HTMLElement | null>(null);
const background = ref<HTMLElement | null>(null);
const colorMap = {
  surface: 'var(--color-surface-300)',
  overlay: 'var(--color-surface-200)',
  item: 'var(--color-surface-100)'
}
const radiusMap = {
  item: 1 * 1.618033988749 ** 1
} as Record<string, number>
radiusMap['overlay'] = radiusMap['item']! * 1.618033988749
radiusMap['surface'] = radiusMap['overlay']! * 1.618033988749

const props = withDefaults(defineProps<{
  depth?: 'surface' | 'overlay' | 'item'
  opacity?: number
}>(), {
  depth: 'surface',
  opacity: 1
})

const backgroundColor: ComputedRef<string> = computed(() => {
  return colorMap[props.depth] || colorMap['surface']
})
const borderRadius: ComputedRef<number> = computed(() => {
  return radiusMap[props.depth] || 1 * 1.618033988749 ** 2
})

defineExpose({
  content,
  background
});
</script>

<style scoped>
.ui-card {
  position: relative;
}
.card-background {
  position: absolute;
  z-index: -1;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
}
</style>