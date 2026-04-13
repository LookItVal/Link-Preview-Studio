<template>
  <div>
    <UIColorModeSwitcher />
    <UICookieConsentModal />
    <div class="flex max-w-5xl p-5 m-auto items-center justify-center min-h-screen">
      <NuxtPage />
    </div>
  </div>
</template>

<script setup lang="ts">
useColorMode()
</script>
