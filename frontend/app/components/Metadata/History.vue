<template>
  <div v-if="history.length" class="w-full max-w-xl space-y-2">
    <MetadataHistoryCard
      v-for="entry in history"
      :key="entry.timestamp"
      :entry="entry"
    />
  </div>
</template>

<script setup lang="ts">
const { history } = useHistory()
</script>