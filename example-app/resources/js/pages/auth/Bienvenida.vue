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
          <input type="text" v-model="email" placeholder="correo" />
          <input type="password" v-model="password" placeholder="contraseña" />
          <a href="/recuperar">¿Has olvidado tu contraseña?</a>
          <button @click="login">Ingresar</button>
          <p v-if="error" class="mensaje-error">{{ error }}</p>
        </div>
      </div>
    </main>

    <Exterior pie />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios' // 📦 Cliente HTTP para consumir API
import { router } from '@inertiajs/vue3'
import Exterior from '@/components/Exterior.vue'

// 📌 Datos del formulario
const email = ref('')
const password = ref('')
const error = ref('')

// 💬 Frases que se rotan en el lateral izquierdo
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

// 🔁 Cambia la frase cada 5 segundos
onMounted(() => {
  let index = 0
  setInterval(() => {
    index = (index + 1) % frases.length
    fraseActual.value = frases[index]
  }, 5000)
})

// 🟢 Función que hace login contra el backend Laravel
async function login() {
  try {
    const response = await axios.post('/api/login/alumno', {
      correo: email.value,
      password: password.value
    })

    // ✅ Login exitoso: guarda el alumno en localStorage (opcional)
    localStorage.setItem('alumno', JSON.stringify(response.data.alumno))

    // Redirige a la vista protegida del alumno
    router.visit('/alumno/materias')
  } catch (err) {
    // ❌ Si falla el login, muestra el mensaje de error
    error.value = err.response?.data?.message || 'Error al iniciar sesión'
  }
}
</script>

<style src="@/../css/bienvenida.css"></style>
