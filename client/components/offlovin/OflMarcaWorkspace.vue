<template>
  <span v-if="false" />
</template>

<script setup>
// OFFLOVIN: el panel toma el color del espacio actual (workspace.settings.marca.color).
// Tailwind 4 pinta con variables, así que basta con redefinir la paleta azul, que es la principal de la app.
import { tailwindcssPaletteGenerator } from '~/lib/colors.js'

const { current } = useCurrentWorkspace()
const color = computed(() => current.value?.settings?.marca?.color || null)

const estilo = computed(() => {
  if (!color.value) return null
  const paleta = tailwindcssPaletteGenerator(color.value).primary
  const variables = Object.entries(paleta).map(([tono, valor]) => `--color-blue-${tono}:${valor};`).join('')
  return `html:root{${variables}}`
})

useHead({
  style: computed(() => estilo.value ? [{ key: 'ofl-marca-workspace', innerHTML: estilo.value }] : [])
})
</script>
