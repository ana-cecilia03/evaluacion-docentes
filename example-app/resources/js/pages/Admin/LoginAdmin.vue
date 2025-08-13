<template>
  <div class="page">
    <!-- Encabezado -->
    <header class="header">
      <img src="/img/logo.png" />
    </header>

    <!-- Contenido principal -->
    <main class="main-login">
      <div class="login-container">
        <div class="login-box">
          <h2>Administrador</h2>
          <h3> </h3>

          <input
            type="email"
            v-model="correo"
            placeholder="Correo institucional"
            required
          />
          <input
            type="password"
            v-model="password"
            placeholder="Contraseña"
            required
          />

          <a href="/recuperar">¿Has olvidado tu contraseña?</a>

          <button @click="login">Ingresar</button>

          <p v-if="error" class="mensaje-error">{{ error }}</p>
          <p v-if="success" class="mensaje-exito">{{ success }}</p>
        </div>
      </div>
    </main>

    <!-- Pie de página -->
    <footer class="footer">
      Sistema de encuestas © UNIVERSIDAD POLITÉCNICA DE TEXCOCO
    </footer>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

const correo = ref('')
const password = ref('')
const error = ref('')
const success = ref('')

async function login() {
  error.value = ''
  success.value = ''

  try {
    const response = await fetch('/api/admin/login', {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        correo: correo.value,
        password: password.value
      })
    })

    const data = await response.json()

    if (!response.ok) {
      error.value = data.message || 'correo o contraseña'
      return
    }

    success.value = data.message
    localStorage.setItem('adminToken', data.token)

    // Redirigir al panel de administración
    router.visit('/admin/dashboard')

  } catch (err) {
    error.value = 'Error al conectar con el servidor'
  }
}
</script>

<style src="@/../css/global.css"></style>