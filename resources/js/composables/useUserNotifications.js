import { ref, onMounted, onUnmounted } from 'vue'
import axios from '@/axios'
import { toast } from 'vue3-toastify'
import { useAppointmentStore } from '@/stores/appointments'

export function useUserNotifications() {
  const notifications = ref([])
  let appointmentStore = null
  let interval = null

  const startPolling = () => {
    interval = setInterval(async () => {
      try {
        const { data } = await axios.get('/user/notifications/check-new')

        if (data.notifications && data.notifications.length > 0) {
          data.notifications.forEach(n => {
            toast.info(n.message)

            if (
              n.type === 'appointment_confirmed' ||
              n.type === 'appointment_cancelled_admin' ||
              n.type === 'appointment_cancelled_user' ||
              n.type === 'appointment_attended' // 🟦 NUEVO
            ) {
              appointmentStore.cargarReservas()
            }


            notifications.value.push(n.message)
          })

          const audio = new Audio('/sounds/notify.mp3')
          audio.play()
        }
      } catch (error) {
        console.error('Error en polling usuario:', error)
      }
    }, 5000)
  }

  onMounted(() => {
    appointmentStore = useAppointmentStore() // 🟩 AHORA SÍ REACTIVO
    startPolling()
  })

  onUnmounted(() => clearInterval(interval))

  return {
    notifications
  }
}
