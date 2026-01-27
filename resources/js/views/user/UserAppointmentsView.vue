<template>
  <div>
    <Navbar />

    <div class="p-6">
      <Breadcrumb :items="[
        { label: 'Dashboard', to: '/user/dashboard' },
        { label: 'Mis Reservas' }
      ]" />
    </div>

    <div class="p-6">
      <h2 class="text-2xl font-bold mb-4">Mis Reservas</h2>

      <table class="w-full border-collapse">
        <thead>
          <tr class="bg-gray-100">
            <th class="p-3 text-left">Fecha</th>
            <th class="p-3 text-left">Hora</th>
            <th class="p-3 text-left">Estado</th>
            <th class="p-3 text-left">Acciones</th>
          </tr>
        </thead>

        <tbody>
          <tr v-for="reserva in appointmentStore.reservas" :key="reserva.id" :class="[
            'border-b transition-colors',
            reserva._justUpdated ? 'row-updated' : ''
          ]">
            <td class="p-3">{{ formatDate(reserva.date) }}</td>
            <td class="p-3">{{ reserva.start_time }} - {{ reserva.end_time }}</td>

            <td class="p-3">
              <span :class="{
                'text-green-600 font-bold': reserva.status === 'confirmada',
                'text-yellow-600 font-bold': reserva.status === 'reservada',
                'text-red-600 font-bold': reserva.status === 'cancelada'
              }">
                {{ reserva.status }}
              </span>
            </td>

            <td class="p-3">
              <button v-if="reserva.status === 'reservada' || reserva.status === 'confirmada'"
                class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600" @click="abrirModal(reserva.id)">
                Cancelar
              </button>

            </td>
          </tr>
        </tbody>
      </table>

      <div v-if="appointmentStore.reservas.length === 0" class="text-gray-500 mt-6">
        No tienes reservas aún.
      </div>
    </div>

    <!-- Modal de confirmación -->
    <ModalConfirm :show="modalCancelarOpen" title="Cancelar reserva"
      message="¿Estás seguro de que deseas cancelar esta reserva?" @cancel="modalCancelarOpen = false"
      @confirm="confirmarCancelacion" />
  </div>
</template>

<script setup>
import Navbar from '@/componentes/Navbar.vue'
import Breadcrumb from '@/componentes/Breadcrumb.vue'
import ModalConfirm from '@/componentes/ModalConfirm.vue'

import { useAppointmentStore } from '@/stores/appointments'
import { useUserNotifications } from '@/composables/useUserNotifications'

import { onMounted, ref } from 'vue'
import { toast } from 'vue3-toastify'

const appointmentStore = useAppointmentStore()

// 🟩 Polling global del usuario (confirmaciones/cancelaciones del admin)
const { notifications } = useUserNotifications()

const modalCancelarOpen = ref(false)
const reservaSeleccionada = ref(null)

onMounted(async () => {
  await appointmentStore.cargarReservas()
})

const abrirModal = (id) => {
  reservaSeleccionada.value = id
  modalCancelarOpen.value = true
}

const confirmarCancelacion = async () => {
  modalCancelarOpen.value = false

  await appointmentStore.cancelarReserva(reservaSeleccionada.value)

  toast.success("Reserva cancelada correctamente")

  reservaSeleccionada.value = null
}

const formatDate = (dateString) => {
  if (!dateString) return ''

  // 1. Cortar la hora si viene incluida
  const soloFecha = dateString.split('T')[0].split(' ')[0]

  // 2. Separar manualmente
  const [year, month, day] = soloFecha.split('-')

  // 3. Crear fecha SIN zona horaria
  const fecha = new Date(year, month - 1, day)

  return fecha.toLocaleDateString('es-ES', {
    weekday: 'long',
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  })
}



</script>

<style scoped>
.row-updated {
  animation: highlight 0.8s ease-out;
}

@keyframes highlight {
  0% {
    background-color: #fff3cd;
  }

  100% {
    background-color: transparent;
  }
}
</style>
