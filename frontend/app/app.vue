<template>
  <div>
    <UIColorModeSwitcher />
    <UICookieConsentModal />
    <div ref="pageWrapper">
      <div ref="pageContent">
        <div class="flex max-w-5xl p-5 m-auto items-center justify-center min-h-screen">
          <NuxtPage />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
const { initSmoothScroller } = useSmoothScroller()
const pageWrapper = ref<HTMLElement | null>(null)
const pageContent = ref<HTMLElement | null>(null)
initSmoothScroller(pageWrapper, pageContent)
useColorMode()
</script>
