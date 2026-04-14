export interface HistoryEntry {
  url: string
  status: 'success' | 'error'
  response: any
  timestamp: number
}

const MAX_HISTORY = 10
const HISTORY_STORAGE_KEY = 'search-history'

function loadPersistedHistory(): HistoryEntry[] {
  if (!import.meta.client) {
    return []
  }

  try {
    const raw = window.localStorage.getItem(HISTORY_STORAGE_KEY)
    if (!raw) {
      return []
    }

    const parsed = JSON.parse(raw)
    if (!Array.isArray(parsed)) {
      return []
    }

    return parsed
      .filter((entry: unknown): entry is HistoryEntry => {
        if (!entry || typeof entry !== 'object') {
          return false
        }

        const candidate = entry as HistoryEntry
        return typeof candidate.url === 'string'
          && (candidate.status === 'success' || candidate.status === 'error')
          && typeof candidate.timestamp === 'number'
      })
      .slice(0, MAX_HISTORY)
  }
  catch {
    return []
  }
}

function savePersistedHistory(history: HistoryEntry[]) {
  if (!import.meta.client) {
    return
  }

  window.localStorage.setItem(HISTORY_STORAGE_KEY, JSON.stringify(history.slice(0, MAX_HISTORY)))
}

function clearPersistedHistory() {
  if (!import.meta.client) {
    return
  }

  window.localStorage.removeItem(HISTORY_STORAGE_KEY)
}

export function useHistory() {
  const { cookieSavingEnabled } = useCookieConsent()

  const historyState = useState<HistoryEntry[]>('search-history-state', () => [])

  function syncHistoryFromStorage() {
    if (!cookieSavingEnabled.value) {
      historyState.value = []
      return
    }

    historyState.value = loadPersistedHistory()
  }

  syncHistoryFromStorage()

  watch(cookieSavingEnabled, (enabled) => {
    if (enabled) {
      syncHistoryFromStorage()
      return
    }

    historyState.value = []
  })

  const history = computed(() => historyState.value)

  function addEntry(url: string, status: 'success' | 'error', response: any) {
    const timestamp = Date.now()
    const entry: HistoryEntry = {
      url,
      status,
      response,
      timestamp,
    }

    if (!cookieSavingEnabled.value) {
      historyState.value = [entry, ...historyState.value].slice(0, MAX_HISTORY)
      return
    }

    const nextHistory = [entry, ...historyState.value].slice(0, MAX_HISTORY)
    historyState.value = nextHistory
    savePersistedHistory(nextHistory)
  }

  function clearHistory() {
    if (!cookieSavingEnabled.value) {
      historyState.value = []
      return
    }

    historyState.value = []
    clearPersistedHistory()
  }

  function removeEntry(entryToRemove: HistoryEntry) {
    const nextHistory = historyState.value.filter(entry =>
      !(entry.timestamp === entryToRemove.timestamp && entry.url === entryToRemove.url),
    )

    historyState.value = nextHistory

    if (!cookieSavingEnabled.value) {
      return
    }

    savePersistedHistory(nextHistory)
  }

  return {
    history,
    addEntry,
    clearHistory,
    removeEntry,
  }
}