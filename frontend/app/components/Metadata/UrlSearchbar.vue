<template>
  <UICard depth="overlay" :opacity="0.5">
    <div class="w-full max-w-xl space-y-3 p-(--xs-em)">
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
          :disabled="isLoading"
          :speed="isLoading ? 0 : 3"
        >
          <p class="font-semibold text-(--color-base)">
            Search
          </p>
        </UIShimmeringButton>
      </form>
    </div>
  </UICard>
</template>
<script setup lang="ts">
import { useMetaSearch } from '~/composables/metaSearch'

const inputUrl = ref('')
const { searchResults, isLoading, error, performSearch } = useMetaSearch()
const { addEntry } = useHistory()

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

  inputUrl.value = ''

  await performSearch(url)
  addEntry(url, error.value ? 'error' : 'success', error.value ? { error: error.value } : searchResults.value)
}
</script>