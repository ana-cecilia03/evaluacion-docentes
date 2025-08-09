// resources/js/lib/axios.js
import axios from 'axios'

const instance = axios.create({
  baseURL: '/api', // ⬅️ con esto ya NO pongas /api en cada llamada
  headers: { 'X-Requested-With': 'XMLHttpRequest' }
})

// Lee token en cada request (por si cambia durante la sesión)
instance.interceptors.request.use((config) => {
  const adminToken = localStorage.getItem('adminToken')
  const genericToken = localStorage.getItem('token') // compatibilidad
  const token = adminToken || genericToken

  if (token) config.headers.Authorization = `Bearer ${token}`
  else delete config.headers.Authorization

  return config
})

instance.interceptors.response.use(
  r => r,
  e => {
    if (e.response?.status === 401) {
      console.warn('No autenticado, redirigiendo…')
      // Ajusta según tu flujo:
      window.location.href = '/bienvenida'
    }
    return Promise.reject(e)
  }
)

export default instance
