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
          <h2>Inicio de sesión</h2>
          <h3>Profesores</h3>

          <input
            type="text"
            v-model="email"
            placeholder="Correo institucional"
          />
          <input
            type="password"
            v-model="password"
            placeholder="Contraseña"
          />

          <a href="/recuperar">¿Has olvidado tu contraseña?</a>

          <button @click="login">Ingresar</button>

          <p v-if="error" class="mensaje-error">{{ error }}</p>
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

const email = ref('')
const password = ref('')
const error = ref('')

// Simulación de login para administrador
function login() {
  if (email.value === 'admin@uptex.edu.mx' && password.value === 'admin123') {
    router.visit('/admin/dashboard')
  } else {
    error.value = 'Credenciales incorrectas'
  }
}
</script>

<style src="@/../css/login.css"></style>
