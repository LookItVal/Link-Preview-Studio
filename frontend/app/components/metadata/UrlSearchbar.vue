<template>
  <div class="w-full max-w-xl space-y-3">
    <form class="flex gap-2" @submit.prevent="onSubmit">
      <UICard depth="overlay" class="flex-1">
        <input
          v-model="inputUrl"
          type="url"
          placeholder="https://example.com"
          class="w-full rounded-full px-(--xs-em) py-2"
        >
      </UICard>
      <UIShimmeringButton
        type="submit"
        :disabled="isLoading || !inputUrl.trim()"
      >
        <p class="font-semibold text-(--color-base)">
          Search
        </p>
      </UIShimmeringButton>
    </form>

    <p v-if="error" class="text-sm text-red-500">
      {{ error }}
    </p>

    <pre class="min-h-24 overflow-auto rounded border bg-white/5 p-3 text-xs">{{ formattedResult }}</pre>
  </div>
</template>
<script setup lang="ts">
import { useMetaSearch } from '~/composables/metaSearch'

const inputUrl = ref('')
const { searchResults, isLoading, error, performSearch } = useMetaSearch()

const formattedResult = computed(() => {
  if (!searchResults.value) {
    return 'No result yet.'
  }
  return JSON.stringify(searchResults.value, null, 2)
})

async function onSubmit() {
  const url = inputUrl.value.trim()

  if (!url) {
    return
  }

  await performSearch(url)
}
</script>