<template>
  <div class="calendar-app-container">
    <FullCalendar ref="calendarRef" :options="calendarOptions" />

    <ModalHorario
      :open="modalOpen"
      :initialData="modalData"
      @close="modalOpen = false"
      @save="crearHorario"
    />
  </div>
</template>

<script setup>
import { reactive, ref, onMounted } from 'vue'
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import interactionPlugin from '@fullcalendar/interaction'
import axios from '@/axios'
import { toast } from 'vue3-toastify'
import ModalHorario from '@/componentes/ModalHorario.vue'

const calendarRef = ref(null)
const eventos = ref([])
const modalOpen = ref(false)
const modalData = ref(null)

/**
 * Cargar eventos desde el backend y transformarlos
 */
const cargarEventos = async () => {
  const { data } = await axios.get('/admin/schedules')

  eventos.value = data.map(item => ({
    id: item.id,
    title: item.is_available ? 'Disponible' : 'No disponible',
    start: item.start,
    end: item.end,
    is_available: item.is_available,
    backgroundColor: item.is_available ? '#22c55e' : '#ef4444',
    borderColor: item.is_available ? '#16a34a' : '#dc2626'
  }))
}

/**
 * OPCIONES DEL CALENDARIO
 */
const calendarOptions = reactive({
  plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
  initialView: 'timeGridWeek',
  headerToolbar: {
    left: 'prev,next today',
    center: 'title',
    right: 'dayGridMonth,timeGridWeek,timeGridDay'
  },
  locale: 'es',
  slotLabelInterval: "00:30:00",
  slotDuration: "00:30:00",
  slotMinTime: '08:00:00',
  slotMaxTime: '18:00:00',
  scrollTime: '08:00:00',
  allDaySlot: false,

  height: 'auto',
  editable: false,
  selectable: true,

  slotLabelFormat: {
    hour: 'numeric',
    minute: '2-digit',
    hour12: true
  },

  /**
   * FullCalendar siempre leerá el array actualizado
   */
  events: (info, success) => {
    success(eventos.value)
  },

  /**
   * Recargar cuando cambias de semana, mes o día
   */
  datesSet: async () => {
    await cargarEventos()
    calendarRef.value.getApi().refetchEvents()
  },

  /**
   * Clic en un evento → cambiar disponibilidad
   */
  eventClick: async (info) => {
    const event = info.event

    try {
      await axios.put(`/admin/schedules/${event.id}`, {
        is_available: !event.extendedProps.is_available
      })

      await cargarEventos()
      calendarRef.value.getApi().refetchEvents()

      toast.success(
        event.extendedProps.is_available
          ? "Horario marcado como NO disponible"
          : "Horario marcado como disponible"
      )
    } catch (error) {
      console.error(error)
      toast.error("No se pudo actualizar el horario")
    }
  },

  /**
   * Selección de rango → abrir modal
   */
  select: (info) => {
    modalData.value = {
      date: info.startStr.split('T')[0],
      start_time: info.startStr.split('T')[1].substring(0, 5),
      end_time: info.endStr.split('T')[1].substring(0, 5)
    }
    modalOpen.value = true
  }
})

/**
 * Cargar eventos al montar
 */
onMounted(async () => {
  try {
    await cargarEventos()
    calendarRef.value.getApi().refetchEvents()
  } catch (error) {
    console.error(error)
    toast.error("Error al cargar los horarios")
  }
})

/**
 * Crear horario desde el modal
 */
const crearHorario = async (form) => {
  try {
    await axios.post('/admin/schedules', form)

    await cargarEventos()
    calendarRef.value.getApi().refetchEvents()

    toast.success("Horario creado correctamente")
    modalOpen.value = false
  } catch (error) {
    console.error(error)
    toast.error("No se pudo crear el horario")
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
