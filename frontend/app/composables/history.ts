export interface HistoryEntry {
  url: string
  status: 'success' | 'error'
  response: any
  timestamp: number
}

interface HistoryCookieRef {
  key: string
  timestamp: number
}

const MAX_HISTORY = 10
const HISTORY_TTL_SECONDS = 60 * 60 * 24 * 30
const HISTORY_INDEX_COOKIE = 'search-history-index'
const HISTORY_ENTRY_PREFIX = 'search-history-entry'

function createHistoryEntryKey(timestamp: number): string {
  return `${HISTORY_ENTRY_PREFIX}-${timestamp}-${Math.random().toString(36).slice(2, 9)}`
}

export function useHistory() {
  const indexCookie = useCookie<HistoryCookieRef[]>(HISTORY_INDEX_COOKIE, {
    default: () => [],
    maxAge: HISTORY_TTL_SECONDS,
    watch: true,
  })

  const historyState = useState<HistoryEntry[]>('search-history-state', () => [])

  function deleteEntryCookie(key: string) {
    const entryCookie = useCookie<HistoryEntry | null>(key, {
      default: () => null,
      maxAge: HISTORY_TTL_SECONDS,
    })
    entryCookie.value = null
  }

  function syncHistoryFromCookies() {
    const refs = indexCookie.value ?? []
    const nextRefs: HistoryCookieRef[] = []
    const nextHistory: HistoryEntry[] = []

    for (const ref of refs) {
      const entryCookie = useCookie<HistoryEntry | null>(ref.key, {
        default: () => null,
        maxAge: HISTORY_TTL_SECONDS,
      })

      if (!entryCookie.value) {
        continue
      }

      nextRefs.push(ref)
      nextHistory.push(entryCookie.value)
    }

    while (nextRefs.length > MAX_HISTORY) {
      const removed = nextRefs.pop()
      nextHistory.pop()

      if (removed) {
        deleteEntryCookie(removed.key)
      }
    }

    indexCookie.value = nextRefs
    historyState.value = nextHistory
  }

  syncHistoryFromCookies()

  const history = computed(() => historyState.value)

  function addEntry(url: string, status: 'success' | 'error', response: any) {
    const refs = indexCookie.value ?? []
    const timestamp = Date.now()
    const key = createHistoryEntryKey(timestamp)
    const entry: HistoryEntry = {
      url,
      status,
      response,
      timestamp,
    }

    const entryCookie = useCookie<HistoryEntry | null>(key, {
      default: () => null,
      maxAge: HISTORY_TTL_SECONDS,
    })
    entryCookie.value = entry

    refs.unshift({ key, timestamp })

    while (refs.length > MAX_HISTORY) {
      const removed = refs.pop()
      if (removed) {
        deleteEntryCookie(removed.key)
      }
    }

    indexCookie.value = refs
    historyState.value = [entry, ...historyState.value].slice(0, MAX_HISTORY)
  }

  function clearHistory() {
    const refs = indexCookie.value ?? []
    for (const ref of refs) {
      deleteEntryCookie(ref.key)
    }

    indexCookie.value = []
    historyState.value = []
  }

  return {
    history,
    addEntry,
    clearHistory,
  }
}