// resources/js/axios.js
import axios from 'axios'

const api = axios.create({
  baseURL: 'http://127.0.0.1:8000/api',
  headers: {
    Accept: 'application/json'
  }
})

// 👉 Interceptor de REQUEST (AGREGADO)
api.interceptors.request.use(config => {
  const token = localStorage.getItem('token')

  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }

  return config
})

// 👉 Interceptor de RESPUESTA 1
api.interceptors.response.use(
  response => response,
  error => {
    const status = error.response?.status
    const url = error.config?.url

    if (status === 401) {
      if (url.includes('/login') || url.includes('/auth/change-password')) {
        return Promise.reject(error)
      }
      localStorage.removeItem('token')
      window.location.href = '/login'
    }

    if (status === 403) {
      alert('No tienes permisos para esta acción.')
    }

    if (status >= 500) {
      console.error('Error del servidor:', error.response?.data)
    }

    return Promise.reject(error)
  }
)

// 👉 Interceptor de RESPUESTA 2
api.interceptors.response.use(response => response, error => {
  const status = error.response?.status
  const url = error.config?.url

  if (status === 401) {
    if (url.includes('/login') || url.includes('/auth/change-password')) {
      return Promise.reject(error)
    }

    localStorage.removeItem('token')
    window.location.href = '/login'
  }

  return Promise.reject(error)
})

export default api
