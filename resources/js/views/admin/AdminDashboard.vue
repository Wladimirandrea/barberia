<!-- resources/js/views/admin/AdminDashboard.vue -->
<script setup>
import Navbar from '@/componentes/Navbar.vue'
import Breadcrumb from '@/componentes/Breadcrumb.vue'
import { useGlobalNotifications } from '@/composables/useGlobalNotifications'

// 🟩 Polling global de notificaciones
const { notifications } = useGlobalNotifications()

// 🟩 Nombre del usuario (lo mantengo igual que antes)
const userName = JSON.parse(localStorage.getItem('user'))?.name ?? 'Administrador'
</script>

<template>
  <div>
    <Navbar />

    <div class="p-6">
      <Breadcrumb :items="[{ label: 'Dashboard' }]" />
      <h2 class="text-xl font-bold">Panel de Administración</h2>
      <p>Bienvenido, {{ userName }}.</p>

      <!-- Botones -->
      <router-link
        to="/admin/users"
        class="mt-4 inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
      >
        Ver Usuarios
      </router-link>

      <router-link
        to="/admin/users/create"
        class="mt-4 inline-block bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 ml-2"
      >
        Crear Usuario
      </router-link>

      <router-link
        to="/admin/schedules"
        class="mt-4 inline-block bg-red-600 text-white px-4 py-2 rounded hover:bg-green-700 ml-2"
      >
        Horarios
      </router-link>

      <router-link
        to="/admin/days-off"
        class="mt-4 inline-block bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700 ml-2"
      >
        DaysOff
      </router-link>

      <router-link
        to="/admin/appointments"
        class="mt-4 inline-block bg-orange-600 text-white px-4 py-2 rounded hover:bg-orange-700 ml-2"
      >
        Citas
      </router-link>

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
