<template>
  <div>
    <UIMenuBar
      type="logo"
      :animateOnMount="true"
      startPosition="start"
      :scrollLag="true"
      class="pb-5"
    />
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
      <MetadataUrlSearchbar :class="hasHistory ? 'mb-(--xs-em)' : ''"/>
      <MetadataHistory />
    </UICard>
  </div>
</template>

<script setup lang="ts">
const { history } = useHistory()

const hasHistory = ref(false)

onMounted(() => {
  hasHistory.value = history.value.length > 0
})

watch(() => history.value.length > 0, (val) => {
  hasHistory.value = val
})
</script>