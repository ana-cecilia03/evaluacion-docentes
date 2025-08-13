<template>
  <div class="bienvenida-login-page apps-page">
    <!-- Encabezado -->
    <Exterior />

    <main class="contenido">
      <!-- HERO -->
      <section class="hero-app">
        <div class="hero-copy">
          <h1>Descarga la App</h1>
          <p class="tagline">Disponible para Alumnos</p>
          <p class="tagline">Disponible en las tiendas</p>

          <div class="store-badges">
            <a href="#" aria-label="Disponible en Google Play">
              <img src="/img/tiendasApp.png" alt="Disponible en Google Play" />
            </a>
          </div>
        </div>

        <div class="hero-phone">
          <img src="/img/celular.png" alt="Teléfono escaneando código QR" />
        </div>
      </section>

      <!-- QR + PASOS -->
      <section class="qr-steps">
        <div class="qr-image">
          <img src="/img/qrtem.png" alt="Código QR de descarga" />
        </div>

        <div class="steps-copy">
          <h2>Escanear código QR</h2>
          <p class="intro">
            Para comenzar a utilizar la aplicación sigue estos pasos:
          </p>
          <ul class="steps-list">
            <li>Descarga y abre la aplicación</li>
            <li>Escanea este código QR</li>
            <li>Ingresa con tu usuario y contraseña</li>
          </ul>
        </div>
      </section>
    </main>

    <!-- Pie -->
    <Exterior pie />
  </div>
</template>

<script setup>
import Exterior from '@/components/Exterior.vue'
</script>

<style src="@/../css/global.css"></style>