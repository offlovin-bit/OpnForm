import { httpUrl } from '~/lib/self-hosted-public.js'

export function useSelfHostedPresentation() {
  const config = useRuntimeConfig().public

  return {
    // OFFLOVIN: our name and icon by default
    name: config.selfHostedName || 'OFFLOVIN',
    logoUrl: httpUrl(config.selfHostedLogoUrl) || '/offlovin/icon.png',
    privacyPolicyUrl: httpUrl(config.selfHostedPrivacyPolicyUrl),
    termsUrl: httpUrl(config.selfHostedTermsUrl),
  }
}
