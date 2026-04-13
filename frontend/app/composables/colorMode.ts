type ColorMode = 'dark' | 'light' | 'system'
type ResolvedColorMode = 'dark' | 'light'

export function useColorMode() {
  const mode = useState<ColorMode>('color-mode', () => 'system')
  const systemMode = useState<ResolvedColorMode>('system-color-mode', () => 'dark')

  function detectSystemMode(): ResolvedColorMode {
    if (!import.meta.client || !window.matchMedia) {
      return 'dark'
    }

    return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'
  }

  const resolvedMode = computed<ResolvedColorMode>(() => {
    return mode.value === 'system' ? systemMode.value : mode.value
  })

  if (import.meta.client) {
    systemMode.value = detectSystemMode()

    const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)')
    const onSystemModeChange = (event: MediaQueryListEvent) => {
      systemMode.value = event.matches ? 'dark' : 'light'
    }

    mediaQuery.addEventListener('change', onSystemModeChange)

    watchEffect(() => {
      document.body.classList.remove('dark', 'light')
      document.body.classList.add(resolvedMode.value)
    })

    onScopeDispose(() => {
      mediaQuery.removeEventListener('change', onSystemModeChange)
    })
  }

  return {
    mode: readonly(mode),
    resolvedMode: readonly(resolvedMode),
    systemMode: readonly(systemMode),
    setMode: (newMode: ColorMode) => { mode.value = newMode },
    toggle: () => {
      mode.value = resolvedMode.value === 'dark' ? 'light' : 'dark'
    }
  }
}
