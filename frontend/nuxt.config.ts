// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2025-07-15',
  devtools: { enabled: true },
  app: {
    head: {
      title: 'Link Preview Studio',
      meta: [
        { name: 'description', content: 'Preview and inspect how your links appear on social media platforms. Test Open Graph, Twitter Cards, and other metadata in real time.' }
      ]
    }
  },
  runtimeConfig: {
    public: {
      apiBase: 'https://link-preview-studio-api.lookitval.com/api'
    }
  },
  css: ['@/assets/css/main.css'],
  modules: [
    '@nuxt/a11y',
    '@nuxt/icon',
    '@nuxt/image',
    '@nuxtjs/tailwindcss'
  ]
})