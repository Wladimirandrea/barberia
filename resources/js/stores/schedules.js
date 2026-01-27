
import { defineStore } from 'pinia'
import axios from '@/axios'

export const useScheduleStore = defineStore('schedules', {
  state: () => ({
    eventos: []
  }),

  actions: {
    async cargarEventos() {
      const { data } = await axios.get('/schedules/available')

      this.eventos = data.map(item => ({
        id: item.id,
        title: 'Disponible',
        start: item.start,
        end: item.end,
        backgroundColor: '#22c55e',
        borderColor: '#16a34a',
        extendedProps: {
          id: item.id,
          date: item.date,
          start_time: item.start_time,
          end_time: item.end_time
        }
      }))
    }
  }
})
