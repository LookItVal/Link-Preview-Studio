<template>
  <div
    v-if="showModal"
    class="fixed inset-0 z-60 flex items-end justify-center bg-(--color-crust)/70 p-4 sm:items-center"
    role="dialog"
    aria-modal="true"
    aria-labelledby="cookie-consent-title"
  >
    <UICard depth="surface" class="w-full max-w-lg p-(--xs-em)">
      <div class="space-y-3 p-(--xs-em)">
        <h2 id="cookie-consent-title" class="text-base font-semibold text-(--color-text)">
          Cookie Preferences
        </h2>
        <p class="text-sm text-(--color-subtext-100)">
          This site uses cookies for things like recent search history and your display preferences.
        </p>
        <div class="flex items-center justify-end gap-2">
          <button
            type="button"
            class="rounded-full cursor-pointer px-(--xs-em) py-2 text-sm font-semibold text-(--color-subtext-100) hover:text-(--color-text)"
            @click="declineCookies"
          >
            Decline
          </button>
          <button
            type="button"
            class="rounded-full cursor-pointer bg-(--color-green) px-(--xs-em) py-2 text-sm font-semibold text-(--color-base)"
            @click="acceptCookies"
          >
            Accept
          </button>
        </div>
      </div>
    </UICard>
  </div>
</template>

<script setup lang="ts">
const { hasMadeChoice, acceptCookies, declineCookies } = useCookieConsent()

const showModal = computed(() => !hasMadeChoice.value)
</script>
