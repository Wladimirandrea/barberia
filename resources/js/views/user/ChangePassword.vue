<template>
  <div>
    <Navbar />

    <div class="p-6 max-w-md mx-auto">
      <h1 class="text-2xl font-bold mb-4">Cambiar Contraseña</h1>

      <form @submit.prevent="changePassword" class="space-y-4">
        <!-- Contraseña actual -->
        <input v-model="currentPassword" type="password" placeholder="Contraseña actual" class="border p-2 w-full" />
        <p v-if="error?.current_password" class="text-red-500 text-sm">{{ error.current_password[0] }}</p>

        <!-- Nueva contraseña -->
        <input v-model="newPassword" type="password" placeholder="Nueva contraseña" class="border p-2 w-full" />
        <p v-if="error?.new_password" class="text-red-500 text-sm">{{ error.new_password[0] }}</p>

        <!-- Confirmación nueva contraseña -->
        <input v-model="newPasswordConfirmation" type="password" placeholder="Confirmar nueva contraseña"
          class="border p-2 w-full" />
        <p v-if="error?.new_password_confirmation" class="text-red-500 text-sm">{{ error.new_password_confirmation[0] }}
        </p>

        <!-- Botón -->
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
          Actualizar Contraseña
        </button>
      </form>

      <!-- Mensaje de éxito -->
      <transition name="fade">
        <div v-if="success" class="mt-4 bg-green-600 text-white px-4 py-2 rounded shadow-lg">
          {{ success }}
        </div>
      </transition>

      <!-- Mensaje de error general -->
      <transition name="fade">
        <div v-if="error?.general" class="mt-4 bg-red-600 text-white px-4 py-2 rounded shadow-lg">
          {{ error.general[0] }}
        </div>
      </transition>
    </div>
  </div>
</template>

<script setup>
import Navbar from '@/componentes/Navbar.vue'
import { ref } from 'vue'
import { useRouter } from 'vue-router'   // 👈 importa router
import api from '../../axios'
import { useAuthStore } from '../../stores/auth'

const auth = useAuthStore()
const router = useRouter()               // 👈 instancia router

const currentPassword = ref('')
const newPassword = ref('')
const newPasswordConfirmation = ref('')
const error = ref({})
const success = ref(null)

const changePassword = async () => {
  error.value = {}
  success.value = null

  try {
    const res = await api.post('/auth/change-password', {
      current_password: currentPassword.value,
      new_password: newPassword.value,
      new_password_confirmation: newPasswordConfirmation.value
    }, {
      headers: { Authorization: `Bearer ${auth.token}` }
    })

    success.value = res.data.message
    currentPassword.value = ''
    newPassword.value = ''
    newPasswordConfirmation.value = ''

    // 👇 redirige al dashboard de usuario después de éxito
    setTimeout(() => {
      router.push('/user/dashboard')
    }, 1500) // espera 1.5s para mostrar el mensaje antes de redirigir
  } catch (err) {
    console.error('Error en changePassword:', err.response?.data)
    error.value = err.response?.data?.errors || { general: [err.response?.data?.message || 'Error al cambiar contraseña'] }
  }
}
</script>


<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
