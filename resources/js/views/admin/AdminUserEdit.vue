<!-- resources/js/views/admin/AdminUserEdit.vue -->
<template>
  <div>
    <Navbar />

    <div class="p-6 max-w-lg mx-auto">
      <Breadcrumb :items="[
        { label: 'Dashboard', to: '/admin/dashboard' },
        { label: 'Usuarios', to: '/admin/users' },
        { label: 'Editar Usuario' }
      ]" />

      <h1 class="text-2xl font-bold mb-4">Editar Usuario</h1>

      <form @submit.prevent="updateUser" class="space-y-4">
        <input v-model="name" type="text" placeholder="Nombre" class="border p-2 w-full" />
        <p v-if="error?.name" class="text-red-500 text-sm">{{ error.name[0] }}</p>

        <input v-model="email" type="email" placeholder="Email" class="border p-2 w-full" />
        <p v-if="error?.email" class="text-red-500 text-sm">{{ error.email[0] }}</p>

        <input v-model="password" type="password" placeholder="Nueva Contraseña (opcional)" class="border p-2 w-full" />
        <p v-if="error?.password" class="text-red-500 text-sm">{{ error.password[0] }}</p>

        <select v-model="role" class="border p-2 w-full">
          <option value="admin">Admin</option>
          <option value="user">Usuario</option>
        </select>
        <p v-if="error?.role" class="text-red-500 text-sm">{{ error.role[0] }}</p>

        <input v-model="phone" type="text" placeholder="Teléfono" class="border p-2 w-full" />
        <p v-if="error?.phone" class="text-red-500 text-sm">{{ error.phone[0] }}</p>

        <input v-model="address" type="text" placeholder="Dirección" class="border p-2 w-full" />
        <p v-if="error?.address" class="text-red-500 text-sm">{{ error.address[0] }}</p>

        <div v-if="imagenActual" class="mb-2">
          <p class="text-sm text-gray-600">Imagen actual:</p>
          <img :src="imagenActual" alt="avatar" class="w-16 h-16 rounded-full border" />
        </div>

        <div v-if="previewNuevaImagen" class="mb-2">
          <p class="text-sm text-gray-600">Nueva imagen seleccionada:</p>
          <img :src="previewNuevaImagen" alt="preview" class="w-16 h-16 rounded-full border" />
        </div>

        <input type="file" @change="handleFileChange" accept="image/png,image/jpeg" class="border p-2 w-full" />
        <p v-if="error?.imagen" class="text-red-500 text-sm">{{ error.imagen[0] }}</p>

        <div class="flex gap-4">
          <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">
            Actualizar
          </button>
        </div>
      </form>

      <transition name="fade">
        <div v-if="toastMessage" class="fixed bottom-4 right-4 bg-green-600 text-white px-4 py-2 rounded shadow-lg">
          {{ toastMessage }}
        </div>
      </transition>

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
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '../../axios'
import { useAuthStore } from '../../stores/auth'
import Breadcrumb from '@/componentes/Breadcrumb.vue'
import Navbar from '@/componentes/Navbar.vue'
import { useAdminNotifications } from '@/composables/useAdminNotifications'

// 🟩 Notificaciones globales del admin
const { userName, notifications } = useAdminNotifications()

const auth = useAuthStore()
const route = useRoute()
const router = useRouter()

const name = ref('')
const email = ref('')
const password = ref('')
const role = ref('user')
const phone = ref('')
const address = ref('')
const imagenActual = ref(null)
const nuevaImagen = ref(null)
const previewNuevaImagen = ref(null)

const error = ref({})
const toastMessage = ref(null)

const handleFileChange = (e) => {
  const file = e.target.files[0] || null
  if (file) {
    nuevaImagen.value = file
    previewNuevaImagen.value = URL.createObjectURL(file)
  } else {
    nuevaImagen.value = null
    previewNuevaImagen.value = null
  }
}

onMounted(async () => {
  try {
    const res = await api.get(`/admin/users/${route.params.id}`, {
      headers: { Authorization: `Bearer ${auth.token}` }
    })

    const user = res.data.user
    name.value = user.name
    email.value = user.email
    role.value = user.role
    phone.value = user.phone
    address.value = user.address
    imagenActual.value = user.imagen
  } catch (err) {
    error.value = { general: [err.response?.data?.message || 'Error al cargar usuario'] }
  }
})

const updateUser = async () => {
  error.value = {}
  toastMessage.value = null

  try {
    const formData = new FormData()
    formData.append('name', name.value || '')
    formData.append('email', email.value || '')
    formData.append('role', role.value || '')
    formData.append('phone', phone.value || '')
    formData.append('address', address.value || '')

    if (password.value) {
      formData.append('password', password.value)
    }

    if (nuevaImagen.value instanceof File) {
      formData.append('imagen', nuevaImagen.value)
    }

    formData.append('_method', 'PUT')

    const res = await api.post(`/admin/users/${route.params.id}`, formData, {
      headers: {
        Authorization: `Bearer ${auth.token}`
      }
    })

    toastMessage.value = res.data.message || 'Usuario actualizado correctamente'
    imagenActual.value = res.data.user.imagen + '?t=' + Date.now()
    previewNuevaImagen.value = null
    nuevaImagen.value = null
    password.value = ''

    setTimeout(() => {
      router.push('/admin/users')
    }, 2000)
  } catch (err) {
    error.value = err.response?.data?.errors || { general: ['Error al actualizar usuario'] }
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
