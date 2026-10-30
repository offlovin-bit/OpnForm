<template>
  <aside
    :class="[
      'bg-neutral-100 border-neutral-200 flex flex-col transition-all duration-300 ease-in-out z-[60]',
      isMobileMenuOpen
        ? 'fixed inset-0'
        : 'sticky top-0 w-full h-[49px] overflow-hidden sm:flex sm:fixed sm:h-full sm:w-58 sm:border-r',
    ]"
  >
    <!-- Top Section - Only show if there's header content or on mobile -->
    <div 
      v-if="hasHeaderContent || isMobileMenuOpen"
      class="p-1 border-b border-neutral-200 h-[49px]"
    >
      <div class="flex items-center justify-between gap-1 w-full">
        <!-- Header Content Slot -->
        <slot name="header" :isMobileMenuOpen="isMobileMenuOpen" />
        
        <div class="grow" v-if="hasMobileHeaderContent">
          <slot name="mobile-header" :isMobileMenuOpen="isMobileMenuOpen" />
        </div>

        <!-- Mobile Menu Toggle -->
        <div :class="{ 'sm:hidden': !isMobileMenuOpen }">
          <UButton
            square
            size="xl"
            class="hover:bg-neutral-200/80"
            :icon="isMobileMenuOpen ? 'i-heroicons-x-mark' : 'i-heroicons-bars-3'"
            variant="ghost"
            color="neutral"
            @click="isMobileMenuOpen = !isMobileMenuOpen"
          />
        </div>
      </div>
    </div>
    
    <!-- Mobile Menu Toggle (when header is hidden on desktop) -->
    <div 
      v-else
      class="sm:hidden p-1 border-b border-neutral-200 h-[49px] flex items-center justify-start gap-2"
    >
      <div class="grow">
        <slot name="mobile-header" :isMobileMenuOpen="isMobileMenuOpen" />
      </div>
      <UButton
        square
        size="xl"
        class="hover:bg-neutral-200/80"
        icon="i-heroicons-bars-3"
        variant="ghost"
        color="neutral"
        @click="isMobileMenuOpen = !isMobileMenuOpen"
      />
    </div>

    <!-- Navigation Content -->
    <nav 
      class="flex-1 p-2 overflow-y-auto flex flex-col"
      :class="{ 'hidden': !isMobileMenuOpen, 'sm:flex': true }"
    >
      <slot name="navigation" :isMobileMenuOpen="isMobileMenuOpen" />
    </nav>

    <!-- Footer -->
    <div 
      class="p-2 border-t border-neutral-200"
      :class="{ 'hidden': !isMobileMenuOpen, 'sm:block': true }"
    >
      <slot name="footer" :isMobileMenuOpen="isMobileMenuOpen">
        <!-- OFFLOVIN: brand wordmark instead of "OpnForm vX.Y.Z" -->
        <NuxtLink class="flex justify-center" :to="{ name: 'home' }">
          <img src="/offlovin/logo.png" alt="OFFLOVIN" class="h-4 w-auto opacity-60">
        </NuxtLink>
        <!-- OFFLOVIN: oferta del código fuente que exige la AGPLv3 (§13) -->
        <a
          class="mt-2 block text-center text-xs text-neutral-400 hover:text-neutral-600"
          href="https://github.com/offlovin-bit/OpnForm/tree/offlovin"
          target="_blank"
          rel="noopener"
        >Código fuente (AGPLv3)</a>
      </slot>
    </div>
  </aside>
</template>

<script setup>
const slots = useSlots()

const isMobileMenuOpen = ref(false)

// Check if header slot has content
const hasHeaderContent = computed(() => {
  return !!(slots.header && slots.header().length > 0)
})

const hasMobileHeaderContent = computed(() => {
  return !!(slots['mobile-header'] && slots['mobile-header']().length > 0)
})

// Handle body overflow when mobile menu is open
watchEffect(() => {
  if (import.meta.client) {
    document.body.classList.toggle('overflow-hidden', isMobileMenuOpen.value)
  }
})

onUnmounted(() => {
  if (import.meta.client) {
    document.body.classList.remove('overflow-hidden')
  }
})

// Expose the mobile menu state to parent components
defineExpose({
  isMobileMenuOpen
})
</script> 