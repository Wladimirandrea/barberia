<template>
  <div class="max-w-sm mx-auto mt-10">
    <h2 class="text-xl font-bold mb-4">Registro</h2>
    <form @submit.prevent="register">
      <input v-model="name" type="text" placeholder="Nombre"
             class="border p-2 w-full mb-2" required />
      <input v-model="email" type="email" placeholder="Email"
             class="border p-2 w-full mb-2" required />
      <input v-model="password" type="password" placeholder="Contraseña"
             class="border p-2 w-full mb-2" required />
      <input v-model="password_confirmation" type="password" placeholder="Confirmar Contraseña"
             class="border p-2 w-full mb-2" required />

      <button class="bg-green-500 text-white px-4 py-2 w-full">Registrarse</button>
    </form>

    <p v-if="error" class="text-red-500 mt-2">{{ error }}</p>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/axios'
import { useAuthStore } from '@/stores/auth'

const name = ref('')
const email = ref('')
const password = ref('')
const password_confirmation = ref('')
const error = ref('')

const router = useRouter()
const auth = useAuthStore()

const register = async () => {
  error.value = ''

  try {
    const res = await api.post('/register', {
      name: name.value,
      email: email.value,
      password: password.value,
      password_confirmation: password_confirmation.value
    })

    // Guardar en Pinia + localStorage
    auth.setAuth({
      token: res.data.token,
      user: res.data.user,
      role: res.data.role
    })

    // Redirigir según rol
    if (auth.role === 'admin') {
      router.push('/admin/dashboard')
    } else {
      router.push('/user/dashboard')
    }
  } catch (err) {
    error.value = err.response?.data?.message || 'Error en el registro'
  }
}
</script>
