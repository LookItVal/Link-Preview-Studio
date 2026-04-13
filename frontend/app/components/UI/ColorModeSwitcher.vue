<template>
  <div class="fixed bottom-4 left-4 z-50">
    <UICard depth="overlay" :opacity="0.7" class="w-min">
      <div class="flex items-center gap-1 p-1">
        <button
          v-for="option in options"
          :key="option.value"
          type="button"
        class="rounded-full px-2 py-1 text-xs font-semibold transition-colors"
        :class="mode === option.value ? 'bg-(--color-surface-100) text-(--color-text)' : 'text-(--color-subtext-100) hover:text-(--color-text)'"
        :aria-pressed="mode === option.value"
        @click="setMode(option.value)"
      >
        {{ option.label }}
      </button>
    </div>
  </UICard>
  </div>
</template>

<script setup lang="ts">
const { mode, setMode } = useColorMode()

const options = [
  { value: 'light' as const, label: 'Light' },
  { value: 'dark' as const, label: 'Dark' },
  { value: 'system' as const, label: 'System' },
]
</script>
