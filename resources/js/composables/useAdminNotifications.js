import { ref, onMounted } from 'vue'
import { toast } from 'vue3-toastify'
import { usePolling } from '@/composables/usePolling'

export function useAdminNotifications() {
  const API = import.meta.env.VITE_API_URL

  // 🔊 Control de sonido
  const sonidoHabilitado = ref(false)
  function activarSonido() {
    sonidoHabilitado.value = true
  }

  // 🔔 Notificaciones en pantalla
  const notifications = ref([])

  // 🔐 Usuario logueado
  const userName = ref('')
  const user = localStorage.getItem('user')
  if (user) {
    const parsed = JSON.parse(user)
    userName.value = parsed.name
  }

  // 🟦 Habilitar sonido después de la primera interacción
  onMounted(() => {
    window.addEventListener('click', activarSonido, { once: true })
    window.addEventListener('keydown', activarSonido, { once: true })
  })

  /* ---------------------------------------------------
     🔵 POLLING: NUEVOS USUARIOS
  --------------------------------------------------- */
  usePolling(`${API}/admin/users/check-new`, (data) => {
    if (data.hasNewUsers) {
      toast.info('Nuevo usuario registrado 🚀')

      if (sonidoHabilitado.value) {
        new Audio('/sounds/notify.mp3').play()
      }

      notifications.value.push('Nuevo usuario registrado')

      if (Notification.permission === 'granted') {
        new Notification('Nuevo usuario registrado 🚀', {
          body: 'Se ha creado un nuevo usuario en el sistema',
          icon: '/icons/user.png'
        })
      }
    }
  }, 10000)

  /* ---------------------------------------------------
     🔴 POLLING: NUEVAS CITAS
  --------------------------------------------------- */
  usePolling(`${API}/admin/appointments/check-new`, (data) => {
    if (data.hasNewAppointments) {
      toast.info('Nueva cita registrada 📅')

      if (sonidoHabilitado.value) {
        new Audio('/sounds/notify.mp3').play()
      }

      notifications.value.push('Nueva cita registrada')

      if (Notification.permission === 'granted') {
        new Notification('Nueva cita registrada 📅', {
          body: 'Un usuario ha reservado una nueva cita',
          icon: '/icons/calendar.png'
        })
      }
    }
  }, 5000)

  return {
    userName,
    notifications
  }
}
