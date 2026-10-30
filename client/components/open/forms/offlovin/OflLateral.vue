<template>
  <!-- OFFLOVIN: índice de secciones del modo focused (barra lateral en escritorio, barra superior en móvil) -->
  <aside class="ofl-lateral" aria-label="Progreso del cuestionario">
    <div class="ofl-lateral-cabecera">
      <img v-if="form.logo_picture" :src="form.logo_picture" alt="" class="ofl-logo">
    </div>

    <div class="ofl-lateral-titulo">
      <p class="ofl-nombre">{{ form.title }}</p>
      <p class="ofl-restan" aria-live="polite">{{ restanTexto }}</p>
    </div>

    <ol class="ofl-secciones">
      <li
        v-for="(seccion, i) in secciones"
        :key="seccion.nombre"
        class="ofl-seccion"
        :class="`is-${seccion.estado}`"
      >
        <button
          type="button"
          class="ofl-seccion-boton"
          :disabled="!seccion.alcanzable || seccion.estado === 'actual'"
          :aria-current="seccion.estado === 'actual' ? 'step' : null"
          @click="irA(seccion)"
        >
          <span class="ofl-marca" aria-hidden="true">
            <svg v-if="seccion.estado === 'hecha'" viewBox="0 0 16 16" class="ofl-check">
              <path d="M3.5 8.5l3 3 6-7" />
            </svg>
            <span v-else>{{ i + 1 }}</span>
          </span>
          <span class="ofl-seccion-texto">
            <span class="ofl-seccion-nombre">{{ seccion.nombre }}</span>
            <span v-if="seccion.estado === 'actual' && seccion.preguntas > 1" class="ofl-seccion-paso">
              Pregunta {{ seccion.posicion }} de {{ seccion.preguntas }}
            </span>
          </span>
        </button>
      </li>
    </ol>

    <div class="ofl-lateral-pie">
      <div class="ofl-barra" role="progressbar" :aria-valuenow="porcentaje" aria-valuemin="0" aria-valuemax="100">
        <div class="ofl-barra-relleno" :style="{ width: porcentaje + '%' }" />
      </div>
    </div>
  </aside>

  <div class="ofl-movil" aria-hidden="true">
    <img v-if="form.logo_picture" :src="form.logo_picture" alt="" class="ofl-logo">
    <p class="ofl-movil-seccion">
      <span v-if="seccionActual" class="ofl-movil-nombre">{{ seccionActual.nombre }}</span>
      <span v-if="seccionActual" class="ofl-movil-cuenta">{{ indiceActual + 1 }} de {{ secciones.length }}</span>
      <span v-else class="ofl-movil-cuenta">{{ restanTexto }}</span>
    </p>
    <div class="ofl-movil-barra">
      <div class="ofl-barra-relleno" :style="{ width: porcentaje + '%' }" />
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  formManager: { type: Object, required: true }
})

const form = computed(() => props.formManager.config.value)
const structure = props.formManager.structure
const state = computed(() => props.formManager.state)

const pasos = computed(() => (structure?.value?.fieldGroups?.value || []).map(grupo => grupo?.[0]).filter(Boolean))
const esPregunta = (campo) => campo && !String(campo.type || '').startsWith('nf-')
const enviado = computed(() => !!state.value?.isSubmitted)
const paginaActual = computed(() => state.value?.currentPage ?? 0)

// Cada paso hereda la sección del anterior; los pasos antes de la primera sección (la intro) no tienen.
const secciones = computed(() => {
  const lista = []
  let actualNombre = null
  pasos.value.forEach((campo, indice) => {
    if (campo.section) actualNombre = campo.section
    if (!actualNombre) return
    let seccion = lista[lista.length - 1]
    if (!seccion || seccion.nombre !== actualNombre) {
      seccion = { nombre: actualNombre, inicio: indice, fin: indice, indices: [] }
      lista.push(seccion)
    }
    seccion.fin = indice
    if (esPregunta(campo)) seccion.indices.push(indice)
  })
  return lista.map((seccion) => {
    let estado = 'pendiente'
    if (enviado.value || paginaActual.value > seccion.fin) estado = 'hecha'
    else if (paginaActual.value >= seccion.inicio) estado = 'actual'
    const posicion = seccion.indices.filter(i => i <= paginaActual.value).length
    return {
      ...seccion,
      estado,
      preguntas: seccion.indices.length,
      posicion: Math.max(posicion, 1),
      alcanzable: !enviado.value && seccion.inicio < paginaActual.value
    }
  })
})

const indiceActual = computed(() => secciones.value.findIndex(s => s.estado === 'actual'))
const seccionActual = computed(() => secciones.value[indiceActual.value] || null)

const restantes = computed(() => pasos.value.filter((campo, i) => i >= paginaActual.value && esPregunta(campo)).length)
const restanTexto = computed(() => {
  if (enviado.value) return 'Enviado'
  if (restantes.value === 0) return 'Último paso'
  if (restantes.value === 1) return 'Queda una pregunta'
  return `Quedan ${restantes.value} preguntas`
})

const porcentaje = computed(() => {
  if (enviado.value) return 100
  const total = pasos.value.length
  if (!total) return 0
  return Math.round((paginaActual.value / total) * 100)
})

const irA = (seccion) => {
  if (!seccion.alcanzable || state.value?.isProcessing) return
  props.formManager.state.currentPage = seccion.inicio
  if (import.meta.client) window.scrollTo({ top: 0, behavior: 'smooth' })
}
</script>

<style>
/* OFFLOVIN: temas del cuestionario. El tema sale de form.settings.tema. */
.ofl-shell {
  --ofl-ancho-lateral: 320px;
  --ofl-tinta: #26374b;
  --ofl-tinta-suave: #556476;
  --ofl-fondo: #ffffff;
  background: var(--ofl-fondo);
  color: var(--ofl-tinta);
  caret-color: var(--ofl-acento-claro, #d02c2f);
}
.ofl-shell ::selection { background: rgb(208 44 47 / 0.16); color: var(--ofl-tinta); }

.ofl-tema-marino {
  --ofl-lat-fondo: #26374b;
  --ofl-lat-texto: #ffffff;
  --ofl-lat-suave: rgb(255 255 255 / 0.66);
  --ofl-lat-linea: rgb(255 255 255 / 0.18);
  --ofl-lat-acento: #ff5a6e;
  --ofl-lat-acento-texto: #26374b;
  --ofl-logo-filtro: brightness(0) invert(1);
}
.ofl-tema-claro {
  --ofl-lat-fondo: #eef1f5;
  --ofl-lat-texto: #26374b;
  --ofl-lat-suave: #556476;
  --ofl-lat-linea: #d3d9e1;
  --ofl-lat-acento: #d02c2f;
  --ofl-lat-acento-texto: #ffffff;
  --ofl-logo-filtro: none;
}
.ofl-tema-rojo {
  --ofl-lat-fondo: #c4282c;
  --ofl-lat-texto: #ffffff;
  --ofl-lat-suave: #ffe2e2;
  --ofl-lat-linea: rgb(255 255 255 / 0.3);
  --ofl-lat-acento: #ffffff;
  --ofl-lat-acento-texto: #c4282c;
  --ofl-logo-filtro: brightness(0) invert(1);
}

/* Barra lateral (escritorio) */
.ofl-lateral {
  display: none;
}
@media (min-width: 1024px) {
  .ofl-shell { padding-left: var(--ofl-ancho-lateral); }
  .ofl-lateral {
    position: fixed;
    inset: 0 auto 0 0;
    z-index: 30;
    width: var(--ofl-ancho-lateral);
    display: flex;
    flex-direction: column;
    padding: 40px 32px 36px;
    background: var(--ofl-lat-fondo);
    color: var(--ofl-lat-texto);
    overflow-y: auto;
  }
}
@media (min-width: 1440px) {
  .ofl-shell { --ofl-ancho-lateral: 360px; }
  .ofl-lateral { padding: 48px 40px 40px; }
}
.ofl-logo {
  height: 34px;
  width: auto;
  filter: var(--ofl-logo-filtro);
}
.ofl-lateral-titulo { margin-top: 56px; }
.ofl-nombre {
  font-size: 1.375rem;
  line-height: 1.2;
  font-weight: 600;
  letter-spacing: -0.01em;
  text-wrap: balance;
}
.ofl-restan {
  margin-top: 6px;
  font-size: 0.9375rem;
  color: var(--ofl-lat-suave);
  font-variant-numeric: tabular-nums;
}

.ofl-secciones {
  list-style: none;
  margin: 40px 0 0;
  padding: 0;
}
.ofl-seccion { position: relative; }
.ofl-seccion:not(:last-child)::after {
  content: "";
  position: absolute;
  left: 13px;
  top: 34px;
  bottom: 4px;
  width: 1px;
  background: var(--ofl-lat-linea);
}
.ofl-seccion.is-hecha:not(:last-child)::after { background: var(--ofl-lat-suave); }
.ofl-seccion-boton {
  display: grid;
  grid-template-columns: 28px 1fr;
  align-items: start;
  gap: 14px;
  width: 100%;
  min-height: 44px;
  padding: 0 0 22px;
  text-align: left;
  color: inherit;
  background: none;
  border: 0;
  border-radius: 6px;
  cursor: default;
}
.ofl-seccion.is-hecha .ofl-seccion-boton:not(:disabled) { cursor: pointer; }
.ofl-seccion.is-hecha .ofl-seccion-boton:not(:disabled):hover .ofl-seccion-nombre {
  text-decoration: underline;
  text-underline-offset: 4px;
  text-decoration-thickness: 1px;
}
.ofl-seccion-boton:focus-visible {
  outline: 2px solid var(--ofl-lat-acento);
  outline-offset: 4px;
}
.ofl-marca {
  display: grid;
  place-items: center;
  width: 28px;
  height: 28px;
  border-radius: 999px;
  border: 1.5px solid var(--ofl-lat-linea);
  font-size: 0.8125rem;
  font-weight: 600;
  font-variant-numeric: tabular-nums;
  color: var(--ofl-lat-suave);
  transition: background-color 240ms cubic-bezier(0.16, 1, 0.3, 1), border-color 240ms cubic-bezier(0.16, 1, 0.3, 1), color 240ms cubic-bezier(0.16, 1, 0.3, 1);
}
.ofl-seccion.is-actual .ofl-marca {
  background: var(--ofl-lat-acento);
  border-color: var(--ofl-lat-acento);
  color: var(--ofl-lat-acento-texto);
}
.ofl-seccion.is-hecha .ofl-marca {
  background: transparent;
  border-color: var(--ofl-lat-suave);
  color: var(--ofl-lat-texto);
}
.ofl-check {
  width: 14px;
  height: 14px;
  fill: none;
  stroke: currentColor;
  stroke-width: 2;
  stroke-linecap: round;
  stroke-linejoin: round;
  stroke-dasharray: 16;
  stroke-dashoffset: 0;
  animation: ofl-trazo 420ms cubic-bezier(0.16, 1, 0.3, 1) both;
}
@keyframes ofl-trazo { from { stroke-dashoffset: 16; } }
.ofl-seccion-texto {
  display: flex;
  flex-direction: column;
  gap: 2px;
  padding-top: 3px;
}
.ofl-seccion-nombre {
  font-size: 1.0625rem;
  line-height: 1.35;
  font-weight: 500;
  color: var(--ofl-lat-suave);
}
.ofl-seccion.is-actual .ofl-seccion-nombre,
.ofl-seccion.is-hecha .ofl-seccion-nombre { color: var(--ofl-lat-texto); }
.ofl-seccion.is-actual .ofl-seccion-nombre { font-weight: 600; }
.ofl-seccion-paso {
  font-size: 0.875rem;
  color: var(--ofl-lat-suave);
  font-variant-numeric: tabular-nums;
}

.ofl-lateral-pie { margin-top: auto; padding-top: 32px; }
.ofl-barra {
  height: 4px;
  border-radius: 999px;
  background: var(--ofl-lat-linea);
  overflow: hidden;
}
.ofl-barra-relleno {
  height: 100%;
  border-radius: inherit;
  background: var(--ofl-lat-acento);
  transition: width 480ms cubic-bezier(0.16, 1, 0.3, 1);
}

/* Barra superior (móvil y tablet) */
.ofl-movil {
  position: fixed;
  inset: 0 0 auto 0;
  z-index: 30;
  height: 56px;
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 0 20px;
  background: var(--ofl-lat-fondo);
  color: var(--ofl-lat-texto);
}
.ofl-movil .ofl-logo { height: 24px; }
.ofl-movil-seccion {
  margin-left: auto;
  display: flex;
  align-items: baseline;
  gap: 8px;
  min-width: 0;
  font-size: 0.9375rem;
}
.ofl-movil-nombre {
  font-weight: 600;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.ofl-movil-cuenta {
  color: var(--ofl-lat-suave);
  font-variant-numeric: tabular-nums;
  white-space: nowrap;
}
.ofl-movil-barra {
  position: absolute;
  inset: auto 0 0 0;
  height: 3px;
  background: var(--ofl-lat-linea);
}
.ofl-shell { padding-top: 56px; }
@media (min-width: 1024px) {
  .ofl-movil { display: none; }
  .ofl-shell { padding-top: 0; }
}

/* Pregunta: más grande y con la tinta de la marca */
.ofl-shell .input-label {
  font-size: 1.3125rem;
  line-height: 1.3;
  font-weight: 500;
  letter-spacing: -0.01em;
  color: var(--ofl-tinta);
  text-wrap: balance;
}
.ofl-shell .field-help {
  font-size: 1.0625rem;
  line-height: 1.5;
  color: var(--ofl-tinta-suave);
}
.ofl-shell .text-block h1 {
  font-size: clamp(2rem, 1.4rem + 2.4vw, 2.875rem);
  line-height: 1.08;
  font-weight: 600;
  letter-spacing: -0.02em;
  color: var(--ofl-tinta);
  margin-bottom: 0.75rem;
  text-wrap: balance;
}
.ofl-shell .text-block p {
  font-size: 1.1875rem;
  line-height: 1.55;
  color: var(--ofl-tinta-suave);
  max-width: 34em;
}
.ofl-shell .text-block p + p { margin-top: 0.5rem; }
.ofl-shell .text-block a,
.ofl-shell .field-help a {
  color: inherit;
  text-decoration: underline;
  text-underline-offset: 3px;
}
.ofl-shell .btn {
  border-radius: 999px;
  min-height: 48px;
  padding-inline: 1.75rem;
  font-weight: 500;
  letter-spacing: 0.01em;
}
/* Flechas de navegación: discretas, redondas y en la tinta de la marca */
.ofl-shell [aria-label="Form controls"] button {
  border-radius: 999px;
  width: 40px;
  height: 40px;
  justify-content: center;
  background: var(--ofl-fondo) !important;
  color: var(--ofl-tinta) !important;
  box-shadow: inset 0 0 0 1px #d3d9e1;
  transition: background-color 160ms ease-out;
}
.ofl-shell [aria-label="Form controls"] button:hover:not(:disabled) { background: #eef1f5 !important; }
.ofl-shell [aria-label="Form controls"] button:disabled { opacity: 0.35; }
.ofl-shell [aria-label="Form controls"] button:focus-visible { outline: 2px solid var(--ofl-tinta); outline-offset: 2px; }
@media (min-width: 1024px) {
  .ofl-shell .input-label { font-size: 1.625rem; }
}
@media (prefers-reduced-motion: reduce) {
  .ofl-check { animation: none; }
  .ofl-barra-relleno, .ofl-marca { transition: none; }
}
</style>
