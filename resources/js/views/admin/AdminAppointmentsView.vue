<template>
  <div>
    <Navbar />

    <div class="p-6">
      <h2 class="text-2xl font-bold mb-4">Citas Registradas</h2>

      <table class="w-full border-collapse">
        <thead>
          <tr class="bg-gray-100">
            <th class="p-3 text-left">Usuario</th>
            <th class="p-3 text-left">Fecha</th>
            <th class="p-3 text-left">Hora</th>
            <th class="p-3 text-left">Estado</th>
            <th class="p-3 text-left">Acciones</th>
          </tr>
        </thead>

        <tbody>
          <tr
            v-for="cita in adminStore.citas"
            :key="cita.id"
            :class="[
              'border-b transition-colors',
              cita._justUpdated ? 'row-updated' : ''
            ]"
          >
            <td class="p-3">{{ cita.user }}</td>
            <td class="p-3">{{ cita.date }}</td>
            <td class="p-3">{{ cita.start_time }} - {{ cita.end_time }}</td>

            <td class="p-3">
              <span
                :class="{
                  'text-yellow-600 font-bold': cita.status === 'reservada',
                  'text-green-600 font-bold': cita.status === 'confirmada',
                  'text-red-600 font-bold': cita.status === 'cancelada',
                  'text-blue-600 font-bold': cita.status === 'atendido'
                }"
              >
                {{ cita.status }}
              </span>
            </td>

            <td class="p-3">
              <div class="flex gap-2">

                <!-- Confirmar -->
                <button
                  v-if="cita.status === 'reservada'"
                  class="px-3 py-1 bg-green-600 text-white rounded flex items-center gap-1 hover:bg-green-700"
                  @click="confirmar(cita.id)"
                >
                  ✔️ <span>Confirmar</span>
                </button>

                <!-- Cancelar -->
                <button
                  v-if="cita.status !== 'cancelada' && cita.status !== 'atendido'"
                  class="px-3 py-1 bg-red-600 text-white rounded flex items-center gap-1 hover:bg-red-700"
                  @click="abrirModal(cita.id)"
                >
                  ❌ <span>Cancelar</span>
                </button>

                <!-- Atendido -->
                <button
                  v-if="cita.status === 'confirmada'"
                  class="px-3 py-1 bg-blue-600 text-white rounded flex items-center gap-1 hover:bg-blue-700"
                  @click="marcarAtendido(cita.id)"
                >
                  🩺 <span>Atendido</span>
                </button>

              </div>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Notificaciones -->
      <div v-if="notifications.length" class="mt-6">
        <h3 class="font-semibold">Notificaciones</h3>
        <ul class="list-disc pl-6">
          <li v-for="(note, index) in notifications" :key="index">
            {{ note }}
          </li>
        </ul>
      </div>
    </div>

    <!-- Modal de confirmación -->
    <ModalConfirm
      :show="showModal"
      title="Cancelar cita"
      message="¿Estás seguro de que deseas cancelar esta cita?"
      @cancel="showModal = false"
      @confirm="cancelarConfirmado"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import Navbar from '@/componentes/Navbar.vue'
import ModalConfirm from '@/componentes/ModalConfirm.vue'
import { toast } from 'vue3-toastify'
import { useGlobalNotifications } from '@/composables/useGlobalNotifications'
import { useAdminAppointmentsStore } from '@/stores/adminAppointments'

const adminStore = useAdminAppointmentsStore()
const { notifications } = useGlobalNotifications()

// Modal
const showModal = ref(false)
const citaAEliminar = ref(null)

// Cargar citas al entrar
onMounted(() => {
  adminStore.cargarCitas()
})

// Confirmar cita
const confirmar = async (id) => {
  await adminStore.confirmarCita(id)
  toast.success('Cita confirmada')
}

// Abrir modal de cancelación
const abrirModal = (id) => {
  citaAEliminar.value = id
  showModal.value = true
}

// Confirmar cancelación desde el modal
const cancelarConfirmado = async () => {
  if (!citaAEliminar.value) return
  await adminStore.cancelarCita(citaAEliminar.value)
  toast.error('Cita cancelada')
  showModal.value = false
  citaAEliminar.value = null
}

// Marcar como atendido
const marcarAtendido = async (id) => {
  await adminStore.marcarAtendido(id)
  toast.success('Cita marcada como atendida')
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
