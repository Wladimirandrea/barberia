<!-- resources/js/views/admin/AdminUser.vue -->
<template>
  <div>
    <Navbar />

    <div class="p-6">
      <Breadcrumb :items="[
        { label: 'Dashboard', to: '/admin/dashboard' },
        { label: 'Usuarios' }
      ]" />

      <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Usuarios</h1>

        <router-link
          to="/admin/users/create"
          class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"
        >
          Crear Usuario
        </router-link>
      </div>

      <div v-if="usersStore.error" class="text-red-500">{{ usersStore.error }}</div>

      <table v-else class="min-w-full border border-gray-300">
        <thead class="bg-gray-100">
          <tr>
            <th class="px-4 py-2 border">ID</th>
            <th class="px-4 py-2 border">Nombre</th>
            <th class="px-4 py-2 border">Email</th>
            <th class="px-4 py-2 border">Rol</th>
            <th class="px-4 py-2 border">Teléfono</th>
            <th class="px-4 py-2 border">Dirección</th>
            <th class="px-4 py-2 border">Imagen</th>
            <th class="px-4 py-2 border">Acciones</th>
          </tr>
        </thead>

        <tbody>
          <tr v-for="user in usersStore.users" :key="user.id">
            <td class="px-4 py-2 border">{{ user.id }}</td>
            <td class="px-4 py-2 border">{{ user.name }}</td>
            <td class="px-4 py-2 border">{{ user.email }}</td>
            <td class="px-4 py-2 border">{{ user.role }}</td>
            <td class="px-4 py-2 border">{{ user.phone }}</td>
            <td class="px-4 py-2 border">{{ user.address }}</td>

            <td class="px-4 py-2 border">
              <a :href="imageUrl(user.imagen)" target="_blank" rel="noopener">
                <img
                  :src="imageUrl(user.imagen)"
                  alt="avatar"
                  class="w-12 h-12 rounded-full object-cover"
                />
              </a>
            </td>

            <td class="px-4 py-2 border">
              <router-link
                :to="`/admin/users/${user.id}/edit`"
                class="bg-yellow-500 text-white px-2 py-1 rounded hover:bg-yellow-600"
              >
                Actualizar
              </router-link>

              <button
                @click="deleteUser(user.id)"
                class="bg-red-600 text-white px-2 py-1 rounded hover:bg-red-700 ml-2"
              >
                Eliminar
              </button>
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

      <div v-if="usersStore.error" class="text-red-600 mt-4">{{ usersStore.error }}</div>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useAuthStore } from '../../stores/auth'
import { useUsersStore } from '@/stores/users'
import api from '../../axios'
import Breadcrumb from '@/componentes/Breadcrumb.vue'
import Navbar from '@/componentes/Navbar.vue'
import { toast } from 'vue3-toastify'
import { useAdminNotifications } from '@/composables/useAdminNotifications'

// 🟩 Notificaciones globales del admin
const { notifications } = useAdminNotifications()

const auth = useAuthStore()
const usersStore = useUsersStore()

// Imagen segura
function imageUrl(path) {
  if (!path) return '/storage/users/avatar.png'
  if (/^https?:\/\//i.test(path)) return path
  const cleaned = String(path).trim().replace(/^public\//i, '').replace(/^\/?storage\//i, 'storage/')
  return `/storage/${cleaned.replace(/^storage\//i, '')}`
}

// Cargar usuarios al entrar
onMounted(async () => {
  await usersStore.fetchUsers()
})

// Eliminar usuario
const deleteUser = async (id) => {
  if (!confirm('¿Seguro que deseas eliminar este usuario?')) return

  try {
    await api.delete(`/admin/users/${id}`, {
      headers: { Authorization: `Bearer ${auth.token}` }
    })

    usersStore.removeUserById(id)
    toast.success('Usuario eliminado correctamente')
  } catch (err) {
    usersStore.error = err.response?.data?.message || 'Error al eliminar usuario'
  }
}
</script>

<style scoped>
img.object-cover {
  object-fit: cover;
}
</style>
