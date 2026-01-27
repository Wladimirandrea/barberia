import { defineStore } from 'pinia'
import api from '../axios'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    token: localStorage.getItem('token') || null,
    user: JSON.parse(localStorage.getItem('user') || 'null'),
    role: localStorage.getItem('role') || null
  }),

  getters: {
    isLoggedIn: (state) => !!state.token,
    isAdmin: (state) => state.role === 'admin',
    isUser: (state) => state.role === 'user' || state.role === 'usuario'
  },

  actions: {
    setAuth(data) {
      this.token = data.token
      this.user = data.user
      this.role = data.role

      localStorage.setItem('token', data.token)
      localStorage.setItem('user', JSON.stringify(data.user))
      localStorage.setItem('role', data.role)
    },

    async logout() {
      try {
        await api.post('/logout', {}, {
          headers: { Authorization: `Bearer ${this.token}` }
        })
      } catch (err) {
        console.error('Error en logout', err)
      } finally {
        this.clearAuth()
      }
    },

    clearAuth() {
      this.token = null
      this.user = null
      this.role = null

      localStorage.removeItem('token')
      localStorage.removeItem('user')
      localStorage.removeItem('role')
    }
  }
})

