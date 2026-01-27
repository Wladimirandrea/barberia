<template>
  <div>
    <Navbar />
    <div class="p-6">
      <Breadcrumb :items="[
        { label: 'Dashboard', to: '/admin/dashboard' },
        { label: 'Horarios' }
      ]" />
    </div>

    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
      <div class="bg-white rounded shadow p-4">
        <h1 class="text-2xl font-bold mb-4">Calendario</h1>
        <ScheduleCalendar ref="calendarComponent" />
      </div>

      <div class="bg-white rounded shadow p-4">
        <h1 class="text-2xl font-bold mb-4">Generar Rango de Horarios</h1>

        <form @submit.prevent="generateRange" class="space-y-4">
          <!-- Fecha inicio (icono a la izquierda dentro de button) -->
          <div>
            <label class="block text-sm font-medium mb-1">Fecha inicio</label>
            <div class="relative flex items-center">
              <button
                type="button"
                class="icon-btn-left"
                @click="openStartPicker"
                aria-label="Abrir selector de fecha inicio"
                title="Abrir calendario"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                  <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                  <line x1="16" y1="2" x2="16" y2="6"></line>
                  <line x1="8" y1="2" x2="8" y2="6"></line>
                  <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
              </button>

              <input
                ref="startVisibleInput"
                type="text"
                class="date-input pl-10"
                aria-label="Fecha inicio visible"
                readonly
              />
            </div>

            <input ref="startHiddenInput" type="hidden" v-model="start_date" />
            <div class="text-sm text-gray-600 mt-1">Seleccionado: {{ startDateUS || '—' }}</div>
          </div>

          <!-- Fecha fin (icono a la izquierda dentro de button) -->
          <div>
            <label class="block text-sm font-medium mb-1">Fecha fin</label>
            <div class="relative flex items-center">
              <button
                type="button"
                class="icon-btn-left"
                @click="openEndPicker"
                aria-label="Abrir selector de fecha fin"
                title="Abrir calendario"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                  <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                  <line x1="16" y1="2" x2="16" y2="6"></line>
                  <line x1="8" y1="2" x2="8" y2="6"></line>
                  <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
              </button>

              <input
                ref="endVisibleInput"
                type="text"
                class="date-input pl-10"
                aria-label="Fecha fin visible"
                readonly
              />
            </div>

            <input ref="endHiddenInput" type="hidden" v-model="end_date" />
            <div class="text-sm text-gray-600 mt-1">Seleccionado: {{ endDateUS || '—' }}</div>
          </div>

          <div>
            <label class="block text-sm font-medium">Hora inicio</label>
            <select v-model="start_time" class="border p-2 w-full rounded">
              <option value="">Selecciona</option>
              <option v-for="hora in horas" :key="hora" :value="hora">{{ hora }}</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium">Hora fin</label>
            <select v-model="end_time" class="border p-2 w-full rounded">
              <option value="">Selecciona</option>
              <option v-for="hora in horas" :key="hora" :value="hora">{{ hora }}</option>
            </select>
          </div>

          <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Generar
          </button>
        </form>

        <p v-if="success" class="text-green-600 mt-2">{{ success }}</p>
        <p v-if="error" class="text-red-600 mt-2">{{ error }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import Navbar from '@/componentes/Navbar.vue'
import Breadcrumb from '@/componentes/Breadcrumb.vue'
import ScheduleCalendar from '@/componentes/ScheduleCalendar.vue'
import axios from '@/axios'
import { useAuthStore } from '@/stores/auth'
import { toast } from 'vue3-toastify'

import flatpickr from 'flatpickr'
import 'flatpickr/dist/flatpickr.min.css'

const auth = useAuthStore()
const calendarComponent = ref(null)

const start_date = ref('')
const end_date = ref('')
const start_time = ref('')
const end_time = ref('')
const success = ref(null)
const error = ref(null)

const startVisibleInput = ref(null)
const endVisibleInput = ref(null)
const startHiddenInput = ref(null)
const endHiddenInput = ref(null)

let fpStart = null
let fpEnd = null

const horas = []
for (let h = 8; h <= 18; h++) {
  horas.push(`${String(h).padStart(2, '0')}:00`)
  if (h < 18) horas.push(`${String(h).padStart(2, '0')}:30`)
}

function formatDateUS(isoDate) {
  if (!isoDate) return ''
  const parts = isoDate.split('-')
  if (parts.length !== 3) return ''
  const [yyyy, mm, dd] = parts
  return `${mm}/${dd}/${yyyy}`
}

const startDateUS = computed(() => (start_date.value ? formatDateUS(start_date.value) : ''))
const endDateUS = computed(() => (end_date.value ? formatDateUS(end_date.value) : ''))

onMounted(() => {
  fpStart = flatpickr(startVisibleInput.value, {
    altInput: true,
    altFormat: 'm/d/Y',
    dateFormat: 'Y-m-d',
    allowInput: true,
    defaultDate: start_date.value || null,
    onChange: (selectedDates, dateStr) => {
      start_date.value = dateStr || ''
    }
  })

  fpEnd = flatpickr(endVisibleInput.value, {
    altInput: true,
    altFormat: 'm/d/Y',
    dateFormat: 'Y-m-d',
    allowInput: true,
    defaultDate: end_date.value || null,
    onChange: (selectedDates, dateStr) => {
      end_date.value = dateStr || ''
    }
  })
})

onBeforeUnmount(() => {
  if (fpStart) fpStart.destroy()
  if (fpEnd) fpEnd.destroy()
})

function openStartPicker() {
  if (fpStart && typeof fpStart.open === 'function') fpStart.open()
}
function openEndPicker() {
  if (fpEnd && typeof fpEnd.open === 'function') fpEnd.open()
}

const generateRange = async () => {
  success.value = null
  error.value = null

  if (!start_date.value || !end_date.value || !start_time.value || !end_time.value) {
    error.value = 'Completa todos los campos'
    toast.error(error.value)
    return
  }

  if (new Date(end_date.value) < new Date(start_date.value)) {
    error.value = 'La fecha fin debe ser igual o posterior a la fecha inicio'
    toast.error(error.value)
    return
  }

  try {
    const payload = {
      user_id: auth.user.id,
      start_date: start_date.value,
      end_date: end_date.value,
      start_time: start_time.value,
      end_time: end_time.value
    }

    const res = await axios.post('/admin/schedules/generate-range', payload, {
      headers: { Authorization: `Bearer ${auth.token}` }
    })

    success.value = res.data.message || 'Rango generado correctamente'
    toast.success(success.value)

    start_date.value = ''
    end_date.value = ''
    start_time.value = ''
    end_time.value = ''

    if (fpStart) fpStart.clear()
    if (fpEnd) fpEnd.clear()

    if (calendarComponent.value && calendarComponent.value.reloadCalendar) {
      await calendarComponent.value.reloadCalendar()
    } else {
      window.location.reload()
    }
  } catch (err) {
    console.error('generateRange error', err)
    const serverMessage = err.response?.data?.message
    const serverErrors = err.response?.data?.errors
    if (serverErrors) {
      const firstKey = Object.keys(serverErrors)[0]
      error.value = serverErrors[firstKey][0]
    } else {
      error.value = serverMessage || 'Error al generar rango'
    }
    toast.error(typeof error.value === 'string' ? error.value : 'Error al generar rango')
  }
}
</script>

<style scoped>
.date-input {
  width: 100%;
  padding: 0.6rem 0.75rem;
  box-sizing: border-box;
  border: 1px solid #e6e7eb;
  border-radius: 0.5rem;
  background: #fff;
  color: #111827;
  font-size: 0.95rem;
  height: 2.75rem;
  -webkit-appearance: none;
  appearance: none;
}
.date-input.pl-10 { padding-left: 2.5rem; }

/* Icon button left */
.icon-btn-left {
  position: absolute;
  left: 0.5rem;
  top: 50%;
  transform: translateY(-50%);
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 2rem;
  height: 2rem;
  border: none;
  background: transparent;
  color: #374151;
  cursor: pointer;
  padding: 0;
  border-radius: 0.375rem;
  z-index: 10;
}
.icon-btn-left:focus { outline: none; box-shadow: 0 0 0 3px rgba(59,130,246,0.12); }
.icon-btn-left:hover { color: #1f2937; }

.flatpickr-input[readonly] { background-color: #fff; }

.date-input:focus,
.flatpickr-input:focus {
  outline: none;
  box-shadow: 0 0 0 3px rgba(59,130,246,0.12);
  border-color: #3b82f6;
}
</style>
