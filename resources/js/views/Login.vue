<template>
  <div class="max-w-sm mx-auto mt-10">
    <h2 class="text-xl font-bold mb-4">Login</h2>
    <form @submit.prevent="login">
      <input 
        v-model="email" 
        type="email" 
        placeholder="Email"
        class="border p-2 w-full mb-2"
      />
      <input 
        v-model="password" 
        type="password" 
        placeholder="Password"
        class="border p-2 w-full mb-2"
      />
      <button 
        class="bg-blue-500 text-white px-4 py-2 w-full"
      >
        Entrar
      </button>
    </form>

    <!-- Mostrar error si existe -->
    <p v-if="error" class="text-red-500 mt-2">{{ error }}</p>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/axios'              // 👈 usa tu instancia axios
import { useAuthStore } from '@/stores/auth' // 👈 tu store Pinia

const email = ref('')
const password = ref('')
const error = ref('')
const router = useRouter()
const auth = useAuthStore()

const login = async () => {
  error.value = '' // limpiar antes de intentar

  try {
    const res = await api.post('/login', {
      email: email.value,
      password: password.value
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
    error.value = err.response?.data?.message || 'Credenciales inválidas'
  }
}
</script>
