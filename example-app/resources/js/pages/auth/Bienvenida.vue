<template>
  <div class="bienvenida-login-page">
    <Exterior />

    <main class="contenido">
      <div class="izquierda">
        <img src="/img/evaluacion-docente.jpg" alt="Evaluación docente" />
        <p class="frase">{{ fraseActual }}</p>
      </div>

      <div class="derecha">
        <div class="login-box">
          <div class="icono">
            <img src="/img/login.png" alt="Usuario" />
          </div>
          <h2>Inicio de sesión</h2>
          <hr />

          <input type="email" v-model="email" placeholder="Correo" required />
          <input type="password" v-model="password" placeholder="Contraseña" required />        

          <button @click="login" :disabled="loading">
            <span v-if="loading">Ingresando…</span>
            <span v-else>Ingresar</span>
          </button>

          <p v-if="error" class="mensaje-error">{{ error }}</p>
        </div>
      </div>
    </main>

    <Exterior pie />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import Exterior from '@/components/Exterior.vue'
import api from '@/lib/axios' // ⬅️ instancia con baseURL '/api' y Bearer automático

// Datos del formulario
const email = ref('')
const password = ref('')
const error = ref('')
const loading = ref(false)

// Frases laterales
const frases = [
  'Tu opinión es clave para mejorar la calidad educativa.',
  'Evaluar a los docentes es contribuir al crecimiento de la educación.',
  'La participación activa en la evaluación ayuda a crear un entorno educativo más efectivo.',
  'La calidad de la educación empieza con la retroalimentación de los que viven el aula.',
  'A través de la evaluación, creamos docentes más preparados para el reto de enseñar.',
  'La retroalimentación continua es el camino hacia la mejora constante en la enseñanza.',
  'Tu participación activa en la evaluación de docentes fortalece la educación de nuestra comunidad.',
]
const fraseActual = ref(frases[0])

onMounted(() => {
  let index = 0
  setInterval(() => {
    index = (index + 1) % frases.length
    fraseActual.value = frases[index]
  }, 5000)
})

// Login real contra backend (Alumno)
async function login () {
  error.value = ''
  loading.value = true

  try {
    // Importante: NO antepongas /api (el wrapper ya lo agrega)
    const { data } = await api.post('/login/alumno', {
      correo: email.value,
      password: password.value
    })

    // Guarda alumno y token en claves unificadas
    localStorage.setItem('alumno', JSON.stringify(data.alumno))
    localStorage.setItem('token', data.token)

    // Limpia claves antiguas por compatibilidad
    localStorage.removeItem('alumnoToken')
    localStorage.removeItem('adminToken')

    // Redirige a la vista de materias del alumno
    router.visit('/alumno/materias')
  } catch (e) {
    error.value =
      e?.response?.data?.message ||
      (e?.response?.status === 422 ? 'Datos inválidos' : 'Error al iniciar sesión')
  } finally {
    loading.value = false
  }
}
</script>

