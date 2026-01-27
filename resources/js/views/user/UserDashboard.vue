<template>
  <div>
    <!-- Navbar arriba -->
    <Navbar />

    <!-- Contenido del panel -->
    <div class="p-6">
      <h2 class="text-xl font-bold">Panel de Usuario</h2>
      <p>Bienvenido, {{ userName }}.</p>
    </div>

    <router-link 
      to="/user/change-password" 
      class="inline-block mt-4 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
    >
      Cambiar Contraseña
    </router-link>

    <router-link 
      to="/user/schedule" 
      class="inline-block mt-4 ml-4 bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"
    >
      Reservar Horario
    </router-link>


    <router-link 
      to="/user/appointments" 
      class="inline-block mt-4 ml-4 bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700"
    >
      Mis reservas
    </router-link>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import Navbar from '@/componentes/Navbar.vue'

const userName = ref('Usuario')

onMounted(() => {
  const user = localStorage.getItem('user')
  if (user) {
    const parsed = JSON.parse(user)
    userName.value = parsed.name
  }
})
</script>
