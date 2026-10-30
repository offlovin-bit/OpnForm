<template>
  <!-- OFFLOVIN: registro con la marca (componente OflAuthLayout) -->
  <OflAuthLayout>
    <div v-if="showAppSumoPanel" class="mb-6">
      <AppSumoRegister />
    </div>

    <h1 data-testid="register-page" class="ofl-auth-titulo">
      Crea tu usuario
    </h1>

    <template v-if="!useFeatureFlag('self_hosted') || isInvited">
      <p v-if="isInvited" class="ofl-auth-sub">
        Te han invitado a un espacio de trabajo. Elige una contraseña y entras.
      </p>
      <div class="ofl-auth-form">
        <RegisterForm />
      </div>
    </template>
    <div v-else class="ofl-auth-aviso">
      Para entrar necesitas una invitación. Pídesela a quien gestiona tu espacio, o
      <NuxtLink :to="{ name: 'login' }">entra con tu usuario</NuxtLink>.
    </div>
  </OflAuthLayout>
</template>

<script setup>
import RegisterForm from "~/components/pages/auth/components/RegisterForm.vue"
import AppSumoRegister from "~/components/vendor/appsumo/AppSumoRegister.vue"
import OflAuthLayout from "~/components/offlovin/OflAuthLayout.vue"

definePageMeta({
  middleware: ["self-hosted", "guest"],
  layout: "empty",
})

defineRouteRules({
  swr: 3600,
})

useOpnSeoMeta({
  title: "Crea tu usuario",
})

const route = useRoute()

const isInvited = computed(() => {
  return route.query?.email && route.query?.invite_token
})

const showAppSumoPanel = computed(() => {
  return Boolean(route.query.appsumo_license || route.query.appsumo_error)
})
</script>
