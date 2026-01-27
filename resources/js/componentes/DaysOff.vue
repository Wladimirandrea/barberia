<template>
  <div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
      <div v-for="d in days" :key="d.key" class="flex items-center justify-between p-3 border rounded">
        <div>
          <div class="font-medium">{{ d.label }}</div>
          <div class="text-xs text-gray-500">Selecciona si quieres trabajar ese día</div>
        </div>

        <div class="flex items-center space-x-3">
          <label class="flex items-center cursor-pointer select-none">
            <input type="checkbox" class="sr-only" :checked="workDays[d.key]"
              @change="onToggle(d.key, $event.target.checked)" />
            <div
              :class="['w-11 h-6 flex items-center rounded-full p-1 transition-colors duration-200', workDays[d.key] ? 'bg-blue-600' : 'bg-gray-300']">
              <div
                :class="['bg-white w-4 h-4 rounded-full shadow transform transition-transform duration-200', workDays[d.key] ? 'translate-x-5' : 'translate-x-0']" />
            </div>
          </label>

          <div class="text-sm">
            <span v-if="workDays[d.key]" class="text-green-600 font-medium">Trabajar</span>
            <span v-else class="text-red-600 font-medium">No trabajar</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref, onMounted } from 'vue'
import daysOffService from '@/services/daysOffService'
import { toast } from 'vue3-toastify'

const days = [
  { key: 'monday', label: 'Lunes' },
  { key: 'tuesday', label: 'Martes' },
  { key: 'wednesday', label: 'Miércoles' },
  { key: 'thursday', label: 'Jueves' },
  { key: 'friday', label: 'Viernes' },
  { key: 'saturday', label: 'Sábado' },
  { key: 'sunday', label: 'Domingo' },
]

// workDays: true = trabajar, false = no trabajar
const workDays = reactive({
  monday: true, tuesday: true, wednesday: true, thursday: true, friday: true, saturday: true, sunday: true
})

const loading = ref(false)

const emit = defineEmits(['saved'])

// dentro de resources/js/componentes/DaysOff.vue
async function loadDaysOff() {
  loading.value = true
  try {
    const data = await daysOffService.fetchDaysOff()
    // data.monday === true  => day off (no trabajar)
    // workDays.monday = !data.monday  => true = trabajar, false = no trabajar
    days.forEach(d => {
      workDays[d.key] = !(data?.[d.key] === true)
    })

    console.log('daysOff from API:', data)
    console.log('workDays mapped:', JSON.parse(JSON.stringify(workDays)))
  } catch (err) {
    console.error('loadDaysOff error', err)
    toast.error('No se pudo cargar la configuración')
  } finally {
    loading.value = false
  }
}



onMounted(loadDaysOff)

// onToggle handler
async function onToggle(key, checked) {
  const previous = workDays[key]
  workDays[key] = !!checked

  // backend expects value = true => day off (no trabajar)
  const payload = { day: key, value: !workDays[key] } // invertimos
  try {
    await daysOffService.toggleDay(payload)
    toast.success('Configuración actualizada')
    emit('saved')
  } catch (err) {
    workDays[key] = previous
    toast.error(err.response?.data?.message || 'Error al actualizar')
  }
}

</script>

<style scoped>
.sr-only {
  position: absolute;
  width: 1px;
  height: 1px;
  padding: 0;
  margin: -1px;
  overflow: hidden;
  clip: rect(0, 0, 0, 0);
  white-space: nowrap;
  border: 0;
}
</style>
