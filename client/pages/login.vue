<template>
  <!-- OFFLOVIN: login con la marca (componente OflAuthLayout) -->
  <OflAuthLayout>
    <h1 data-testid="login-page" class="ofl-auth-titulo">
      Entra en tu cuenta
    </h1>
    <div class="ofl-auth-form">
      <LoginForm />
    </div>
  </OflAuthLayout>
</template>

<script setup>
import LoginForm from "~/components/pages/auth/components/LoginForm.vue"
import OflAuthLayout from "~/components/offlovin/OflAuthLayout.vue"

definePageMeta({
  middleware: "guest",
  layout: "empty",
})

useOpnSeoMeta({
  title: "Entrar",
})
</script>
