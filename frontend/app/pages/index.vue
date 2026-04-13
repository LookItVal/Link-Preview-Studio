<template>
  <div class="page-content">
    <div class="logo" />
    <UICard
      depth="surface"
      class="p-(--m-em)"
    >
      <div class="title flex justify-center items-center pb-(--s-em) text-5xl">
        <UIShimmeringText
          text="Link Preview Studio"
          as="h1"
          fgColor="yellow"
          bgColor="pink"
          :speed="3"
        />
      </div>
      <MetadataUrlSearchbar :class="history.length ? 'mb-(--s-em)' : ''"/>
      <MetadataHistory />
    </UICard>
  </div>
</template>

<script setup lang="ts">
const { history } = useHistory()
</script>