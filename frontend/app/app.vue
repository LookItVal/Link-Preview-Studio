<template>
  <div>
    <div class="page-overlay" :class="{ 'is-visible': overlayVisible }" />
    <div class="page-content" :class="{ 'content-blur': overlayVisible }">
      <UIColorModeSwitcher />
      <UICookieConsentModal />
      <div class="flex max-w-5xl p-5 m-auto items-center justify-center min-h-screen">
        <NuxtPage />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
useColorMode()

const overlayVisible = useState('page-overlay', () => true)
const nuxtApp = useNuxtApp()

onMounted(() => {
  nextTick(() => {
    overlayVisible.value = false
  })
})

nuxtApp.hook('page:finish', () => { overlayVisible.value = false })
</script>

<style>
.page-overlay {
  position: fixed;
  inset: 0;
  background: var(--color-base);
  z-index: 9999;
  opacity: 1;
  transition: opacity 0.4s ease;
  pointer-events: none;
}

.page-overlay:not(.is-visible) {
  opacity: 0;
}

.content-blur {
  filter: blur(12px);
}

.page-content {
  transition: filter 0.4s ease;
}
</style>
