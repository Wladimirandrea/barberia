<!-- resources/js/views/admin/DaysOffView.vue -->
<template>
  <div class="min-h-screen bg-gray-50">
    <Navbar />

    <div class="max-w-7xl mx-auto p-6">
      <Breadcrumb :items="[
        { label: 'Dashboard', to: '/admin/dashboard' },
        { label: 'Configuración Días Off' }
      ]" />

      <h1 class="text-2xl font-semibold mb-6">Configuración de Días Off</h1>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Columna izquierda -->
        <div class="bg-white rounded shadow p-4">
          <h2 class="text-lg font-medium mb-3">Tus días</h2>
          <DaysOff @saved="onDaysOffSaved" />
        </div>

        <!-- Columna derecha -->
        <div class="bg-white rounded shadow p-4">
          <div class="flex items-center justify-between mb-3">
            <h2 class="text-lg font-medium">Calendario</h2>
            <button
              @click="reloadCalendar"
              class="px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700"
            >
              Recargar calendario
            </button>
          </div>

          <ScheduleCalendar ref="calendarComponent" />
        </div>
      </div>

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
  </div>
</template>

<script setup>
import { ref } from 'vue'
import Navbar from '@/componentes/Navbar.vue'
import Breadcrumb from '@/componentes/Breadcrumb.vue'
import DaysOff from '@/componentes/DaysOff.vue'
import ScheduleCalendar from '@/componentes/ScheduleCalendar.vue'
import { useAdminNotifications } from '@/composables/useAdminNotifications'

// 🟩 Notificaciones globales del admin
const { notifications } = useAdminNotifications()

const calendarComponent = ref(null)

async function onDaysOffSaved() {
  if (calendarComponent.value?.reloadCalendar) {
    try {
      await calendarComponent.value.reloadCalendar()
    } catch (err) {
      console.error('Error reloading calendar after days off saved', err)
      window.location.reload()
    }
  } else {
    window.location.reload()
  }
}

async function reloadCalendar() {
  if (calendarComponent.value?.reloadCalendar) {
    await calendarComponent.value.reloadCalendar()
  } else {
    window.location.reload()
  }
}
</script>

<style scoped>
:root {
  --panel-min-height: 420px;
}
.bg-white > .fc {
  min-height: var(--panel-min-height);
}
</style>
