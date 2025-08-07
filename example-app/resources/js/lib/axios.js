// resources/js/lib/axios.js
import axios from 'axios'

// Leer el token del localStorage
const token = localStorage.getItem('token')

// Si existe el token, lo agregamos al header por defecto
if (token) {
  axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
}

// (Opcional) Puedes agregar aquí interceptores para manejar errores 401
axios.interceptors.response.use(
  response => response,
  error => {
    if (error.response?.status === 401) {
      console.warn('No autenticado, redirigiendo...')
      window.location.href = '/bienvenida' // o la ruta que desees
    }
    return Promise.reject(error)
  }
)

export default axios
