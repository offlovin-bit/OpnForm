<template>
  <div class="min-h-screen bg-neutral-50 flex flex-col justify-center sm:px-6 lg:px-8 py-10">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <!-- OFFLOVIN: our wordmark and name -->
      <div class="flex justify-center items-center mb-6">
        <h1>
          <img
            src="/offlovin/logo.png"
            alt="OFFLOVIN"
            class="h-9 w-auto"
          >
        </h1>
      </div>
      
      <p class="mt-2 text-center text-sm text-neutral-600">
        Welcome to OFFLOVIN! Let's get you set up. Create your admin account to start building beautiful forms.
      </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
      <div class="bg-white py-8 px-4 shadow-sm sm:rounded-sm sm:px-10">
        <RegisterForm 
          :is-quick="false"
          :is-setup="true"
          @registered="handleSetupComplete"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import RegisterForm from '~/components/pages/auth/components/RegisterForm.vue'

const { invalidateFlags } = useFeatureFlags()
const router = useRouter()

// Check if setup is required
const setupRequired = useFeatureFlag('setup_required', false)
const selfHosted = useFeatureFlag('self_hosted', false)

// Show 404 if setup not required or not self-hosted
if (!setupRequired || !selfHosted) {
  throw createError({ statusCode: 404, statusMessage: 'Page Not Found' })
}

// SEO
// OFFLOVIN: brand name (the " - OFFLOVIN" suffix comes from the title template)
useOpnSeoMeta({
  title: "Setup",
  description: "Set up your OFFLOVIN instance",
  robots: "noindex, nofollow"
})

definePageMeta({
  layout: 'empty'
})

// Handle successful setup completion
const handleSetupComplete = async () => {
  // Invalidate feature flags to update setup_required status
  await invalidateFlags()
  
  // Show success message
  useAlert().success({
    title: "Setup Complete! 🎉",
    description: "Your OFFLOVIN instance is ready. Time to create your first form!" // OFFLOVIN: brand name
  })
  
  // Redirect to dashboard
  router.push({ name: "home" })
}
</script> 