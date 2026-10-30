<template>
  <AppProvider>
    <div
      id="app"
      class="bg-white dark:bg-notion-dark"
    >
      <NuxtLoadingIndicator color="#2563eb" />
      <NuxtLayout>
        <NuxtPage />
      </NuxtLayout>

      <!-- Third-party services and modals - only load when not on public form pages -->
      <ClientOnly v-if="!isPublicFormPage">
        <div
          v-if="authStore.isImpersonating"
          class="fixed z-[9999] left-0 bottom-0 p-4" id="admin-actions"
        >
          <UFieldGroup size="sm">
            <LazyToolsStopImpersonation />
          </UFieldGroup>
        </div>

        <LazyVendorClarity v-if="vendorsReady && authStore.token && config.public.clarityProjectId" />
        <!-- OFFLOVIN: no FeatureBase changelog/feedback widget -->
        <LazyVendorFeatureBase v-if="false && vendorsReady && authStore.token && config.public.featureBaseOrganization" />
        <!-- OFFLOVIN: colores del espacio actual -->
        <OflMarcaWorkspace v-if="authStore.token" />
        <LazyQuickRegister v-if="appStore.quickLoginModal || appStore.quickRegisterModal || appStore.isUnauthorizedError" />
      </ClientOnly>
    </div>
  </AppProvider>
</template>

<script setup>
import OflMarcaWorkspace from '~/components/offlovin/OflMarcaWorkspace.vue'
const vendorsReady = ref(false)
let vendorTimer
onMounted(() => { vendorTimer = setTimeout(() => { vendorsReady.value = true }, 1500) })
onBeforeUnmount(() => clearTimeout(vendorTimer))
const appStore = useAppStore()
const authStore = useAuthStore()
const config = useRuntimeConfig()
const route = useRoute()
const { locale } = useI18n()

// Check if current page is a public form page (for performance optimization)
const isPublicFormPage = computed(() => route.name === 'forms-slug')

// SEO and head configuration
// OFFLOVIN: no OpnForm default title, description or social preview image
useOpnSeoMeta({
  robots: () => {
    return config.public.env === "production" ? null : "noindex, nofollow"
  },
})

useHead({
  // OFFLOVIN: tab title suffix
  titleTemplate: (titleChunk) => {
    return titleChunk ? `${titleChunk} - OFFLOVIN` : "OFFLOVIN"
  },
  meta: [
    {
      name: 'mobile-web-app-capable',
      content: 'yes'
    },
    {
      name: 'apple-mobile-web-app-status-bar-style',
      content: 'black-translucent'
    },
  ],
  link: [
    {
      rel: 'apple-touch-icon',
      type: 'image/png',
      href: '/offlovin/icon.png' // OFFLOVIN: app icon
    }
  ],
  htmlAttrs: () => ({
    dir: 'ltr',
    lang: locale.value
  })
})
</script>
