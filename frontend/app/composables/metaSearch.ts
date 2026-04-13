export function useMetaSearch() {
  const config = useRuntimeConfig();
  const apiBase = config.public.apiBase.replace(/\/$/, '');
  const endpoint = `${apiBase}/metadata`;

  const searchResults = ref(null);
  const isLoading = ref(false);
  const error = ref<string | null>(null);

  async function performSearch(query: string) {
    isLoading.value = true;
    error.value = null;
    
    try {
      const response = await fetch(endpoint, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ url: query })
      });

      if (!response.ok) {
        const errorPayload = await response.json().catch(() => null);
        const message = errorPayload?.message || `HTTP error! status: ${response.status}`;

        throw new Error(message);
      }

      searchResults.value = await response.json();
    } catch (err) {
      error.value = err instanceof Error ? err.message : String(err);
    } finally {
      isLoading.value = false;
    }
  }

  return {
    searchResults,
    isLoading,
    error,
    performSearch
  };
}