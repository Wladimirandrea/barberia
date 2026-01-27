<!-- resources/js/views/admin/AdminUserCreate.vue -->
<template>
  <div>
    <Navbar />

    <div class="p-6 max-w-lg mx-auto">
      <!-- Breadcrumb -->
      <Breadcrumb :items="[
        { label: 'Dashboard', to: '/admin/dashboard' },
        { label: 'Usuarios', to: '/admin/users' },
        { label: 'Crear Usuario' }
      ]" />

      <h1 class="text-2xl font-bold mb-4">Crear Usuario</h1>

      <form @submit.prevent="createUser" class="space-y-4">
        <input v-model="name" type="text" placeholder="Nombre" class="border p-2 w-full" />
        <input v-model="email" type="email" placeholder="Email" class="border p-2 w-full" />
        <input v-model="password" type="password" placeholder="Contraseña" class="border p-2 w-full" />

        <select v-model="role" class="border p-2 w-full">
          <option value="admin">Admin</option>
          <option value="user">Usuario</option>
        </select>

        <input v-model="phone" type="text" placeholder="Teléfono" class="border p-2 w-full" />
        <input v-model="address" type="text" placeholder="Dirección" class="border p-2 w-full" />

        <!-- Input de imagen -->
        <input type="file" @change="onFileChange" class="border p-2 w-full" />

        <!-- Preview -->
        <div v-if="preview" class="mt-4">
          <p class="text-gray-700 mb-2">Vista previa:</p>
          <img :src="preview" alt="Imagen seleccionada" class="max-h-48 rounded border" />
        </div>

        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">
          Crear
        </button>
      </form>

      <div v-if="success" class="text-green-600 mt-4">{{ success }}</div>
      <div v-if="error" class="text-red-600 mt-4">
        <ul>
          <li v-for="(msg, key) in error" :key="key">{{ msg }}</li>
        </ul>
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
import { useRouter } from 'vue-router'
import api from '../../axios'
import { useAuthStore } from '../../stores/auth'
import Navbar from '@/componentes/Navbar.vue'
import Breadcrumb from '@/componentes/Breadcrumb.vue'
import { useAdminNotifications } from '@/composables/useAdminNotifications'

// 🟩 Notificaciones globales del admin
const { userName, notifications } = useAdminNotifications()

const auth = useAuthStore()
const router = useRouter()

// Campos del formulario
const name = ref('')
const email = ref('')
const password = ref('')
const role = ref('user')
const phone = ref('')
const address = ref('')
const imagen = ref(null)
const preview = ref(null)

const error = ref(null)
const success = ref(null)

const onFileChange = (e) => {
  const file = e.target.files[0]
  if (file) {
    imagen.value = file
    preview.value = URL.createObjectURL(file)
  } else {
    imagen.value = null
    preview.value = null
  }
}

const createUser = async () => {
  error.value = null
  success.value = null

  try {
    const formData = new FormData()
    formData.append('name', name.value)
    formData.append('email', email.value)
    formData.append('password', password.value)
    formData.append('role', role.value)
    formData.append('phone', phone.value)
    formData.append('address', address.value)
    if (imagen.value) {
      formData.append('imagen', imagen.value)
    }

    const res = await api.post('/admin/users', formData, {
      headers: {
        Authorization: `Bearer ${auth.token}`,
        'Content-Type': 'multipart/form-data'
      }
    })

    success.value = res.data.message

    // limpiar formulario
    name.value = ''
    email.value = ''
    password.value = ''
    role.value = 'user'
    phone.value = ''
    address.value = ''
    imagen.value = null
    preview.value = null

    router.push('/admin/users')
  } catch (err) {
    error.value = err.response?.data?.errors || 'Error al crear usuario'
  }
}
</script>
