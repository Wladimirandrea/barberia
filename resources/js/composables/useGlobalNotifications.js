// resources/js/composables/useGlobalNotifications.js
import { ref, onMounted, onUnmounted } from 'vue'
import axios from '@/axios'
import { toast } from 'vue3-toastify'
import { useAdminAppointmentsStore } from '@/stores/adminAppointments'

export function useGlobalNotifications() {
  const notifications = ref([])
  const adminStore = useAdminAppointmentsStore()

  let interval = null

  const startPolling = () => {
    interval = setInterval(async () => {
      try {
        const { data } = await axios.get('/admin/notifications/check-new')

        if (data.notifications && data.notifications.length > 0) {
          data.notifications.forEach(n => {
            // Mostrar toast
            toast.info(n.message)

            // 🟩 Refrescar citas si la notificación es relevante
            if (
              n.type === 'appointment_created' ||
              n.type === 'appointment_confirmed' ||
              n.type === 'appointment_cancelled_admin' ||
              n.type === 'appointment_cancelled_user'
            ) {
              adminStore.cargarCitas()
            }

            // Guardar en lista local
            notifications.value.push(n.message)
          })

          // Reproducir sonido
          const audio = new Audio('/sounds/notify.mp3')
          audio.play()
        }
      } catch (error) {
        console.error('Error en polling global:', error)
      }
    }, 5000)
  }

  onMounted(() => startPolling())
  onUnmounted(() => clearInterval(interval))

  return {
    notifications
  }
}
