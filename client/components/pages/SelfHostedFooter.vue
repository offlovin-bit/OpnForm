<template>
  <footer class="mx-auto flex w-full max-w-5xl flex-col items-center justify-between gap-4 px-6 py-8 text-sm text-muted sm:flex-row">
    <!-- OFFLOVIN: our wordmark instead of "Powered by OpnForm"; no OpnForm Documentation / GitHub links -->
    <img src="/offlovin/logo.png" alt="OFFLOVIN" class="h-5 w-auto opacity-70" />
    <nav aria-label="Instance resources" class="flex flex-wrap justify-center gap-x-5 gap-y-2">
      <a v-if="privacyPolicyUrl" :href="privacyPolicyUrl" class="hover:text-highlighted">Privacy policy</a>
      <a v-if="termsUrl" :href="termsUrl" class="hover:text-highlighted">Terms</a>
    </nav>
  </footer>
</template>

<script setup>
const { privacyPolicyUrl, termsUrl } = useSelfHostedPresentation()
</script>
