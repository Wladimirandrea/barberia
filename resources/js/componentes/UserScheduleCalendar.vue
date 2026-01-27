<template>
  <div class="calendar-app-container">
    <FullCalendar ref="calendarRef" :options="calendarOptions" />

    <ModalReserva :open="modalOpen" :slotData="selectedSlot" @close="modalOpen = false" @confirm="reservar" />
  </div>
</template>

<script setup>
import { reactive, ref, onMounted, computed } from 'vue'
import { useScheduleStore } from '@/stores/schedules'
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import interactionPlugin from '@fullcalendar/interaction'
import axios from '@/axios'
import { toast } from 'vue3-toastify'
import { useRouter } from 'vue-router'
import ModalReserva from '@/componentes/ModalReserva.vue'

// STORE + ROUTER
const scheduleStore = useScheduleStore()
const router = useRouter()

// MODAL
const modalOpen = ref(false)
const selectedSlot = ref(null)

// COMPUTED PARA FULLCALENDAR (CLAVE)
const eventosCalendario = computed(() => scheduleStore.eventos)

// OPCIONES DEL CALENDARIO
const calendarOptions = reactive({
  plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
  initialView: 'timeGridWeek',
  locale: 'es',
  allDaySlot: false,
  slotMinTime: '08:00:00',
  slotMaxTime: '18:00:00',
  editable: false,
  selectable: false,

  events: eventosCalendario,

  eventClick: (info) => {
    selectedSlot.value = info.event.extendedProps
    modalOpen.value = true
  }
})

// CARGAR HORARIOS
onMounted(async () => {
  await scheduleStore.cargarEventos()
})

// CONFIRMAR RESERVA
const reservar = async () => {
  try {
    const response = await axios.post('/appointments', {
      schedule_id: selectedSlot.value.id
    })

    toast.success(response.data.message)
    modalOpen.value = false

    // Actualizar horarios
    await scheduleStore.cargarEventos()

    // Redirigir a Mis Reservas después de 3 segundos
    setTimeout(() => {
      router.push('/user/appointments')
    }, 3000)

  } catch (error) {
    console.error(error)

    // Cerrar modal también en caso de error
    modalOpen.value = false

    toast.error("No se pudo crear la reserva, elige otra fecha o hora")
    setTimeout(() => {
      window.location.reload()
    }, 3000)
    

  }
}

</script>

<style scoped>
.calendar-app-container {
  background: #ffffff;
  padding: 1.5rem;
  border-radius: 1rem;
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
  min-height: 700px;
}
</style>
