<template>
  <div class="w-full max-w-xl space-y-3">
    <form class="flex gap-2" @submit.prevent="onSubmit">
      <input
        v-model="inputUrl"
        type="url"
        placeholder="https://example.com"
        class="flex-1 rounded border px-3 py-2"
      >
      <button
        type="submit"
        :disabled="isLoading || !inputUrl.trim()"
        class="rounded bg-blue-600 px-4 py-2 text-white"
        :class="isLoading ? 'translate-y-px opacity-85 shadow-inner' : 'shadow-md hover:bg-blue-700 active:translate-y-px active:shadow-inner'"
      >
        {{ isLoading ? 'Searching...' : 'Search' }}
      </button>
    </form>

    <p v-if="error" class="text-sm text-red-500">
      {{ error }}
    </p>

    <pre class="min-h-24 overflow-auto rounded border bg-white/5 p-3 text-xs">{{ formattedResult }}</pre>
  </div>
</template>
<script setup lang="ts">
import { useMetaSearch } from '~/composable/useMetaSearch'

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