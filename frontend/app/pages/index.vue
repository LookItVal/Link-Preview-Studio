<template>
  <div>
    <UIMenuBar
      type="logo"
      :animateOnMount="true"
      :scrollLag="true"
      class="absolute top-5 left-1/2 transform -translate-x-1/2"
    />
    <UICard
      depth="surface"
      class="p-(--m-em) mt-[3em]"
      data-flip-id="page-content"
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
      <MetadataUrlSearchbar />
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