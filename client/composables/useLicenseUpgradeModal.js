export function useLicenseUpgradeModal() {
  const getErrorStatus = (error) => {
    return error?.response?.status || error?.status || error?.statusCode
  }

  const getErrorMessage = (error) => {
    return error?.response?._data?.message
      || error?.response?.data?.message
      || error?.data?.message
      || error?.message
      || ''
  }

  const isLicenseError = (error, options = {}) => {
    const message = getErrorMessage(error)
    if (getErrorStatus(error) !== 403) return false

    return message.includes('self-hosted license')
      || message.includes('Enterprise license is required')
      || (options.includeUnauthorized && message === 'This action is unauthorized.')
  }

  // OFFLOVIN: never open the licence upgrade modal. Returning false ("not handled")
  // lets every caller fall back to its normal error message.
  const handleLicenseError = (_error, _options = {}) => false

  return {
    getErrorMessage,
    handleLicenseError,
    isLicenseError
  }
}
