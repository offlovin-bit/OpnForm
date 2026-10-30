<template>
  <!-- OFFLOVIN: login y registro con la marca de offlovin.com (carbón y Figtree) y fondo de dithering animado -->
  <div class="ofl-auth">
    <aside class="ofl-auth-marca">
      <ClientOnly>
        <OflDither />
      </ClientOnly>
      <NuxtLink to="/" class="ofl-auth-logo">
        <img src="/offlovin/logo.png" alt="OFFLOVIN">
      </NuxtLink>
    </aside>

    <main class="ofl-auth-panel">
      <div class="ofl-auth-caja">
        <slot />
      </div>
    </main>
  </div>
</template>

<script setup>
import OflDither from '~/components/offlovin/OflDither.vue'

useHead({
  link: [
    { rel: 'preconnect', href: 'https://fonts.googleapis.com' },
    { rel: 'preconnect', href: 'https://fonts.gstatic.com', crossorigin: '' },
    { rel: 'stylesheet', href: 'https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600&display=swap' }
  ]
})
</script>

<style>
.ofl-auth {
  --ofl-carbon: #0f1113;
  --ofl-tinta: #16181a;
  --ofl-suave: #5f6368;
  --ofl-linea: #d9dcdf;
  min-height: 100svh;
  display: grid;
  grid-template-columns: 1fr;
  grid-template-rows: auto 1fr;
  font-family: Figtree, ui-sans-serif, system-ui, sans-serif;
  color: var(--ofl-tinta);
  background: #ffffff;
}
.ofl-auth ::selection { background: #bee6f5; color: var(--ofl-tinta); }

.ofl-auth-marca {
  position: relative;
  overflow: hidden;
  height: 184px;
  background: var(--ofl-carbon);
  color: #ffffff;
  padding: 24px;
}
.ofl-auth-logo {
  position: relative;
  z-index: 1;
  display: inline-block;
}
.ofl-auth-logo img {
  height: 26px;
  width: auto;
  filter: invert(1);
}

.ofl-auth-panel {
  display: flex;
  justify-content: center;
  padding: 40px 24px 56px;
}
.ofl-auth-caja {
  width: 100%;
  max-width: 400px;
}

@media (min-width: 1024px) {
  .ofl-auth { grid-template-columns: minmax(440px, 44%) 1fr; grid-template-rows: 1fr; }
  .ofl-auth-marca {
    position: sticky;
    top: 0;
    height: 100svh;
    padding: 44px 56px;
  }
  .ofl-auth-logo img { height: 30px; }
  .ofl-auth-panel {
    align-items: center;
    padding: 64px 48px;
  }
}

/* Contenido del panel */
.ofl-auth-titulo {
  font-size: 1.75rem;
  line-height: 1.2;
  font-weight: 600;
  letter-spacing: -0.015em;
  color: var(--ofl-tinta);
}
.ofl-auth-sub {
  margin-top: 8px;
  font-size: 1rem;
  line-height: 1.55;
  color: var(--ofl-suave);
}
.ofl-auth-form { margin-top: 28px; }
.ofl-auth-aviso {
  margin-top: 24px;
  padding: 16px 18px;
  border-radius: 14px;
  background: #f2f3f4;
  font-size: 0.9375rem;
  line-height: 1.55;
  color: var(--ofl-tinta);
}
.ofl-auth-aviso a { font-weight: 600; text-decoration: underline; text-underline-offset: 3px; }

/* Campos y botón del formulario de OpnForm, con la piel de la marca */
.ofl-auth .input-label { font-size: 0.875rem; font-weight: 500; color: var(--ofl-tinta); }
.ofl-auth input:not([type="checkbox"]):not([type="radio"]) {
  min-height: 48px;
  border-radius: 12px;
  border-color: var(--ofl-linea);
  font-size: 1rem;
  box-shadow: none;
}
.ofl-auth input:not([type="checkbox"]):not([type="radio"]):focus {
  outline: none;
  border-color: var(--ofl-tinta);
  box-shadow: 0 0 0 1px var(--ofl-tinta);
}
.ofl-auth input:disabled { background: #f2f3f4; color: var(--ofl-suave); }
.ofl-auth button[type="submit"] {
  min-height: 52px;
  margin-top: 8px;
  border-radius: 999px;
  background: var(--ofl-tinta);
  color: #ffffff;
  font-size: 1rem;
  font-weight: 500;
  justify-content: center;
  transition: background-color 160ms ease-out;
}
.ofl-auth button[type="submit"]:hover:not(:disabled) { background: #2b2e31; }
.ofl-auth button[type="submit"]:focus-visible { outline: 2px solid #bee6f5; outline-offset: 3px; }
.ofl-auth a { color: var(--ofl-tinta); }
</style>
