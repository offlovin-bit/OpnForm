# Cambios de OFFLOVIN sobre OpnForm

Esta rama (`offlovin`) parte de OpnForm v2.8.1 y es la versión que sirve https://formularios.offlovin.com.
Se publica para cumplir la AGPLv3 (§13): quien usa los formularios puede obtener aquí el código fuente.

Cambios (30-oct-2026):

- `client/components/open/forms/OpenForm.vue` y `OpenFormFocused.vue`: se quita el sello «Made with OpnForm».
- `client/components/forms/heavy/PhoneInput.vue`: el país por defecto del teléfono es España (+34) en vez de EE. UU.

Cambios (30-oct-2026, segunda tanda):

- Panel de administración con la marca OFFLOVIN: logo, favicon, títulos de pestaña y textos (`client/public/offlovin/`, `app.vue`, `Navbar.vue`, `BaseSidebar.vue`, `SplitWelcome.vue`, `setup.vue`, login y registro).
- Sin avisos de planes de pago ni de licencia en la interfaz: `PlanTag.vue`, `UpgradeBanner.vue`, `FormCleanings.vue`, `useAppModals.js`, `useLicenseUpgradeModal.js`, pestañas License y Billing de ajustes. La lógica que decide qué funciones están disponibles no se toca.
- Sin enlaces a la web, documentación, comunidad ni chat de soporte de OpnForm (`useSharedNavigation.js`, `crisp.client.js`, `GitHubStar.vue` y otros).
- Formulario público: título de pestaña sin sufijo de marca, favicon = logo del formulario, página de enlace caducado neutra (`NotFoundForm.vue`).
- Modo focused: barra lateral con las secciones del cuestionario y temas de color (`client/components/open/forms/offlovin/OflLateral.vue`, enganchada en `OpenFormFocused.vue`). Cada pregunta puede llevar una clave `section`; el tema sale de `settings.tema` (`marino`, `claro`, `rojo`).
- `docker/nginx.conf`: ruta `/marcas/` para servir logos desde una carpeta montada.

Cambios (30-oct-2026, tercera tanda):

- `api/app/Service/License/SelfHostedSeatLimitService.php`: sin tope de usuarios en la instalación (código AGPLv3).
- Marca por espacio: si `workspace.settings.marca.color` existe, el panel usa ese color (`client/components/offlovin/OflMarcaWorkspace.vue`, montado en `app.vue`), y los formularios nuevos del espacio heredan `workspace.settings.marca.formulario` (`client/pages/forms/create/index.vue`).

No se usa ni se modifica nada de `api/app/Enterprise`, que tiene su propia licencia.
