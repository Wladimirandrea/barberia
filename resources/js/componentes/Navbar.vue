<template>
  <nav class="bg-gray-800 text-white px-6 py-3 flex justify-between items-center">
    <div class="text-xl font-bold">Admin Panel</div>
    <div class="space-x-4">
      <router-link to="/admin/dashboard" class="hover:text-gray-300">Dashboard</router-link>
      <router-link to="/admin/users" class="hover:text-gray-300">Usuarios</router-link>
    </div>
    <button @click="logout" class="bg-red-600 px-3 py-1 rounded hover:bg-red-700">
      Logout
    </button>
  </nav>
</template>

<script setup>
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'

const auth = useAuthStore()
const router = useRouter()

const logout = async () => {
  await auth.logout()
  router.push('/login')
}
</script>
