import { defineStore } from 'pinia'
import api from '@/axios'
import { useAuthStore } from '@/stores/auth'

export const useUsersStore = defineStore('users', {
  state: () => ({
    users: [],
    loading: false,
    error: null
  }),
  actions: {
    async fetchUsers() {
      this.loading = true
      this.error = null
      try {
        const auth = useAuthStore()
        const res = await api.get('/admin/users', {
          headers: { Authorization: `Bearer ${auth.token}` }
        })
        this.users = res.data.users || []
      } catch (err) {
        this.error = err.response?.data?.message || 'Error al cargar usuarios'
      } finally {
        this.loading = false
      }
    },
    addOrUpdateUser(user) {
      const idx = this.users.findIndex(u => u.id === user.id)
      if (idx !== -1) {
        // reemplazar usuario existente
        this.users.splice(idx, 1, user)
      } else {
        // insertar al inicio
        this.users.unshift(user)
      }
    },
    removeUserById(id) {
      this.users = this.users.filter(u => u.id !== id)
    }
  }
})
