import axios from '@/axios'

export default {
  async fetchDaysOff() {
    const res = await axios.get('/admin/days-off') // nota: /admin/days-off
    return res.data
  },

  async toggleDay(payload) {
    const res = await axios.post('/admin/days-off', payload)
    return res.data
  }
}
