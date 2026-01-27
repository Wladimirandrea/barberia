import { ref, onMounted, onUnmounted } from 'vue'

/**
 * Composable genérico para hacer polling a un endpoint.
 *
 * @param {string} endpoint - URL del endpoint a consultar.
 * @param {function} callback - Función que recibe la respuesta JSON.
 * @param {number} interval - Intervalo en milisegundos (por defecto 10000 = 10s).
 *
 * @returns {object} { isRunning } - Estado del polling.
 */
export function usePolling(endpoint, callback, interval = 10000) {
  const isRunning = ref(false)
  let intervalId = null

  async function poll() {
    try {
      const response = await fetch(endpoint, {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('token') || ''}`,
          Accept: 'application/json'
        }
      })

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`)
      }

      const data = await response.json()
      callback(data) // 👈 ejecuta la lógica que le pases
    } catch (error) {
      console.error('Error en polling:', error)
    }
  }

  onMounted(() => {
    isRunning.value = true
    poll() // primera ejecución inmediata
    intervalId = setInterval(poll, interval)
  })

  onUnmounted(() => {
    isRunning.value = false
    if (intervalId) clearInterval(intervalId)
  })

  return { isRunning }
}
