// src/composables/useAdminNotifications.js
import { ref, onMounted } from 'vue'
import { toast } from 'vue3-toastify'
import { usePolling } from '@/composables/usePolling'

export function useAdminNotifications() {
  // Control de sonido: solo se habilita después de interacción del usuario
  const sonidoHabilitado = ref(false)

  function activarSonido() {
    if (!sonidoHabilitado.value) {
      sonidoHabilitado.value = true
      console.log('Sonido habilitado tras interacción del usuario')
    }
  }

  // Habilitamos sonido con la primera interacción real del usuario
  onMounted(() => {
    const enableAudio = () => {
      activarSonido()
      // Limpiamos listeners para no acumular eventos
      window.removeEventListener('click', enableAudio)
      window.removeEventListener('keydown', enableAudio)
      window.removeEventListener('touchstart', enableAudio)
    }

    window.addEventListener('click', enableAudio, { once: false })
    window.addEventListener('keydown', enableAudio, { once: false })
    window.addEventListener('touchstart', enableAudio, { once: false }) // para móviles
  })

  // Notificaciones en pantalla (array para mostrarlas donde quieras)
  const notifications = ref([])

  // Nombre del usuario desde localStorage
  const userName = ref('')
  const user = localStorage.getItem('user')
  if (user) {
    try {
      const parsed = JSON.parse(user)
      userName.value = parsed.name || 'Admin'
    } catch (e) {
      console.warn('Error parseando usuario de localStorage', e)
    }
  }

  // Función auxiliar segura para reproducir sonido
  function playNotificationSound() {
    if (!sonidoHabilitado.value) {
      return // No intentamos reproducir si no está habilitado
    }

    const audio = new Audio('/sounds/notify.mp3')
    audio.volume = 0.6 // un poco más suave, ajusta a tu gusto

    audio.play()
      .then(() => {
        console.log('Notificación sonora reproducida')
      })
      .catch(err => {
        console.warn('No se pudo reproducir el sonido:', err.message)
        // Si quieres, aquí podrías deshabilitar temporalmente o mostrar UI
        // sonidoHabilitado.value = false
      })
  }

  /* ---------------------------------------------------
     POLLING: NUEVOS USUARIOS
  --------------------------------------------------- */
  usePolling('http://127.0.0.1:8000/api/admin/users/check-new', (data) => {
    if (data?.hasNewUsers) {
      toast.info('Nuevo usuario registrado 🚀')

      playNotificationSound()

      notifications.value.push({
        type: 'user',
        message: 'Nuevo usuario registrado',
        time: new Date().toLocaleTimeString()
      })

      if (Notification.permission === 'granted') {
        new Notification('Nuevo usuario registrado 🚀', {
          body: 'Se ha creado un nuevo usuario en el sistema',
          icon: '/icons/user.png'
        })
      }
    }
  }, 10000)

  /* ---------------------------------------------------
     POLLING: NUEVAS CITAS
  --------------------------------------------------- */
  usePolling('http://127.0.0.1:8000/api/admin/appointments/check-new', (data) => {
    if (data?.hasNewAppointments) {
      toast.info('Nueva cita registrada 📅')

      playNotificationSound()

      notifications.value.push({
        type: 'appointment',
        message: 'Nueva cita registrada',
        time: new Date().toLocaleTimeString()
      })

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
    notifications,
    sonidoHabilitado,          // puedes usarlo en la UI si quieres un toggle
    activarSonido             // por si quieres un botón manual
  }
}
