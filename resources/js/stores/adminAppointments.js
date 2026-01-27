import { defineStore } from 'pinia'
import axios from '@/axios'

export const useAdminAppointmentsStore = defineStore('adminAppointments', {
  state: () => ({
    citas: []
  }),

  actions: {
    async cargarCitas() {
      const { data } = await axios.get('/admin/appointments')

      const nuevas = data.map(c => ({
        ...c,
        _justUpdated: true
      }))

      this.citas = nuevas

      setTimeout(() => {
        this.citas = this.citas.map(c => ({
          ...c,
          _justUpdated: false
        }))
      }, 800)
    },

    async confirmarCita(id) {
      await axios.put(`/admin/appointments/${id}/confirm`)
      await this.cargarCitas()
    },

    async cancelarCita(id) {
      await axios.put(`/admin/appointments/${id}/cancel`)
      await this.cargarCitas()
    },

    // 🟦 NUEVA ACCIÓN: marcar como atendido
    async marcarAtendido(id) {
      await axios.put(`/admin/appointments/${id}/attended`)
      await this.cargarCitas()
    }
  }
})
