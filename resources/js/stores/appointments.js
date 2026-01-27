// resources/js/stores/appointments.js
import { defineStore } from 'pinia'
import axios from '@/axios'

export const useAppointmentStore = defineStore('appointments', {
  state: () => ({
    reservas: []
  }),

  actions: {
    async cargarReservas() {
      const { data } = await axios.get('/user/appointments')
      this.reservas = data
    },

    async cancelarReserva(id) {
      await axios.put(`/user/appointments/${id}/cancel`)
      await this.cargarReservas() // 🟩 refresca la lista sin reload
    },
  }
})
