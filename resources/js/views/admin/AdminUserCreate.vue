<!-- resources/js/views/admin/AdminUserCreate.vue -->
<template>
  <div>
    <Navbar />
    <div class="p-6 max-w-lg mx-auto">
      <!-- Breadcrumb -->
      <Breadcrumb :items="[
        { label: 'Dashboard', to: { name: 'admin.dashboard' } },
        { label: 'Usuarios', to: { name: 'admin.users.index' } },
        { label: 'Crear Usuario' }
      ]" />

      <h1 class="text-2xl font-bold mb-6">Crear Nuevo Usuario</h1>

      <form @submit.prevent="createUser" class="space-y-5">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
          <input v-model="form.name" type="text" required placeholder="Nombre completo" class="border p-3 w-full rounded focus:outline-none focus:ring-2 focus:ring-blue-500" />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
          <input v-model="form.email" type="email" required placeholder="correo@ejemplo.com" class="border p-3 w-full rounded focus:outline-none focus:ring-2 focus:ring-blue-500" />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña *</label>
          <input v-model="form.password" type="password" required minlength="8" placeholder="Mínimo 8 caracteres" class="border p-3 w-full rounded focus:outline-none focus:ring-2 focus:ring-blue-500" />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Rol *</label>
          <select v-model="form.role" required class="border p-3 w-full rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="admin">Administrador</option>
            <option value="user">Usuario</option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
          <input v-model="form.phone" type="tel" placeholder="+1 (555) 123-4567" class="border p-3 w-full rounded focus:outline-none focus:ring-2 focus:ring-blue-500" />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
          <input v-model="form.address" type="text" placeholder="Calle, número, ciudad" class="border p-3 w-full rounded focus:outline-none focus:ring-2 focus:ring-blue-500" />
        </div>

        <!-- Input de imagen con label -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Foto de perfil (opcional)</label>
          <input type="file" accept="image/*" @change="onFileChange" class="border p-3 w-full rounded bg-gray-50" />
        </div>

        <!-- Preview de imagen -->
        <div v-if="preview" class="mt-4">
          <p class="text-sm text-gray-600 mb-2">Vista previa:</p>
          <img :src="preview" alt="Vista previa de la imagen" class="max-h-48 object-cover rounded border shadow-sm" />
        </div>

        <!-- Loading / Botón -->
        <button
          type="submit"
          :disabled="isLoading"
          class="bg-blue-600 text-white px-6 py-3 rounded font-medium hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition"
        >
          {{ isLoading ? 'Creando...' : 'Crear Usuario' }}
        </button>
      </form>

      <!-- Mensajes de éxito / error -->
      <div v-if="success" class="mt-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
        {{ success }}
      </div>

      <div v-if="error" class="mt-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
        <p v-if="typeof error === 'string'">{{ error }}</p>
        <ul v-else class="list-disc pl-5">
          <li v-for="(messages, field) in error" :key="field">
            <strong>{{ field }}:</strong> {{ messages.join(', ') }}
          </li>
        </ul>
      </div>

      <!-- Notificaciones globales (si las quieres mostrar aquí también) -->
      <div v-if="notifications.length" class="mt-8 p-4 bg-gray-50 border rounded">
        <h3 class="font-semibold mb-2">Notificaciones recientes</h3>
        <ul class="list-disc pl-6 text-sm text-gray-700">
          <li v-for="(note, index) in notifications.slice(0, 5)" :key="index">{{ note }}</li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/axios' // Asegúrate que axios tenga baseURL configurado
import { useAuthStore } from '@/stores/auth'
import Navbar from '@/components/Navbar.vue' // Ajusta la ruta si es '@/componentes/'
import Breadcrumb from '@/components/Breadcrumb.vue'
import { useGlobalNotifications } from '@/composables/useGlobalNotifications'

const { notifications } = useGlobalNotifications()
const auth = useAuthStore()
const router = useRouter()

// Form reactivo (mejor que refs sueltas)
const form = ref({
  name: '',
  email: '',
  password: '',
  role: 'user',
  phone: '',
  address: ''
})

const imagen = ref(null)
const preview = ref(null)
const isLoading = ref(false)
const success = ref(null)
const error = ref(null)

const onFileChange = (e) => {
  const file = e.target.files?.[0]
  if (file && file.type.startsWith('image/')) {
    imagen.value = file
    // Revoca URL anterior si existe
    if (preview.value) URL.revokeObjectURL(preview.value)
    preview.value = URL.createObjectURL(file)
  } else {
    imagen.value = null
    if (preview.value) URL.revokeObjectURL(preview.value)
    preview.value = null
  }
}

const createUser = async () => {
  error.value = null
  success.value = null
  isLoading.value = true

  try {
    const formData = new FormData()
    Object.keys(form.value).forEach(key => {
      formData.append(key, form.value[key])
    })
    if (imagen.value) {
      formData.append('imagen', imagen.value)
    }

    const res = await api.post('/admin/users', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
        // Authorization ya debería estar en interceptor de axios si lo tienes configurado
      }
    })

    success.value = res.data.message || 'Usuario creado exitosamente'
    
    // Limpieza
    form.value = { name: '', email: '', password: '', role: 'user', phone: '', address: '' }
    imagen.value = null
    if (preview.value) {
      URL.revokeObjectURL(preview.value)
      preview.value = null
    }

    // Redirigir (usa named route si la tienes configurada)
    router.push({ name: 'admin.users.index' }) // o '/admin/users' si no usas names

  } catch (err) {
    if (err.response?.status === 422) {
      error.value = err.response.data.errors // { email: ['El email ya está en uso'], ... }
    } else {
      error.value = err.response?.data?.message || 'Error inesperado al crear el usuario. Intenta de nuevo.'
    }
    console.error('Error creando usuario:', err)
  } finally {
    isLoading.value = false
  }
}

// Opcional: cleanup en destroy (buena práctica para memory leaks)
onUnmounted(() => {
  if (preview.value) URL.revokeObjectURL(preview.value)
})
</script>
