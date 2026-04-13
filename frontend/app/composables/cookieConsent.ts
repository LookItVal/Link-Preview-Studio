export type ConsentChoice = 'accepted'

const CONSENT_COOKIE_NAME = 'cookie-consent-choice'
const HISTORY_STORAGE_KEY = 'search-history'
const LEGACY_HISTORY_INDEX_COOKIE = 'search-history-index'
const HISTORY_ENTRY_PREFIX = 'search-history-entry-'
const CONSENT_TTL_SECONDS = 60 * 60 * 24 * 365

function clearCookieByName(name: string) {
  if (!import.meta.client) {
    return
  }

  document.cookie = `${name}=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/`
}

function clearHistoryCookies() {
  clearCookieByName(LEGACY_HISTORY_INDEX_COOKIE)

  if (!import.meta.client) {
    return
  }

  window.localStorage.removeItem(HISTORY_STORAGE_KEY)

  const cookieNames = document.cookie
    .split(';')
    .map(cookie => cookie.trim().split('=')[0])
    .filter(Boolean)

  for (const cookieName of cookieNames) {
    if (cookieName?.startsWith(HISTORY_ENTRY_PREFIX)) {
      clearCookieByName(cookieName)
    }
  }
}

export function useCookieConsent() {
  const consentCookie = useCookie<ConsentChoice | null>(CONSENT_COOKIE_NAME, {
    default: () => null,
    maxAge: CONSENT_TTL_SECONDS,
    watch: true,
  })

  const declinedThisSession = useState<boolean>('cookie-consent-declined-this-session', () => false)

  const cookieSavingEnabled = computed(() => consentCookie.value === 'accepted')
  const hasMadeChoice = computed(() => cookieSavingEnabled.value || declinedThisSession.value)

  function acceptCookies() {
    declinedThisSession.value = false
    consentCookie.value = 'accepted'
  }

  function declineCookies() {
    clearHistoryCookies()
    clearCookieByName(CONSENT_COOKIE_NAME)
    consentCookie.value = null
    declinedThisSession.value = true
  }

  return {
    consentChoice: readonly(consentCookie),
    hasMadeChoice,
    cookieSavingEnabled,
    acceptCookies,
    declineCookies,
  }
}
