type ColorMode = 'dark' | 'light'

export function useColorMode() {
  const mode = useState<ColorMode>('color-mode', () => 'dark')

  if (import.meta.client) {
    watchEffect(() => {
      document.body.classList.remove('dark', 'light')
      document.body.classList.add(mode.value)
    })
  }

  return {
    mode: readonly(mode),
    setMode: (newMode: ColorMode) => { mode.value = newMode },
    toggle: () => { mode.value = mode.value === 'dark' ? 'light' : 'dark' }
  }
}
