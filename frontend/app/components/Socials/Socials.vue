<template>
  <div class="socials flex justify-between">
    <SocialsGithub ref="github" />
    <SocialsIMDB ref="imdb" />
    <SocialsLinkedIn ref="linkedin" />
    <SocialsInstagram ref="instagram" />
  </div>
</template>

<script lang="ts" setup>
const github: Ref<SocialsElement | null> = ref(null);
const imdb: Ref<SocialsElement | null> = ref(null);
const linkedin: Ref<SocialsElement | null> = ref(null);
const instagram: Ref<SocialsElement | null> = ref(null);
const socials = [github, imdb, linkedin, instagram];

onMounted(() => {
  socials.forEach((element, index) => {
    setTimeout(() => {
      element.value!.$el.classList.add('visible');
      setTimeout(() => {
        element.value!.shimmer();
      }, 400);
    }, (index * 150) + 3000);
  });
});
</script>

<style scoped>
.socials a {
  transition: opacity 0.4s ease-out, transform 0.4s ease-out;

  &:not(.visible) {
    opacity: 0;
    transform: translateY(50px);
  }
  &.visible {
    opacity: 1;
    transform: translateY(0px);
  }
}
</style>
