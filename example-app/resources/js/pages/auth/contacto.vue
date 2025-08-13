<template>
  <div class="bienvenida-login-page">
    <Exterior />

    <div class="inicio-container">
      <section class="contacto">
        <!-- Lado Izquierdo: Contactos -->
        <div class="contacto-texto">
          <h2>Contáctanos en:</h2>
          <ul class="contacto-lista">
            <li>
              <a href="https://api.whatsapp.com/send/?phone=525615883707&text=Bienvenido+a+Servicios+Escolares+de+la+UPTEX&type=phone_number&app_absent=0" target="_blank">
                <img src="/img/whatsApp.png" alt="WhatsApp" />
              </a>
              WhatsApp
            </li>
            <li>
              <a href="https://www.facebook.com/UPTexcoco/" target="_blank">
                <img src="/img/facebook.png" alt="Facebook" />
              </a>
              Facebook
            </li>
            <li>
              <a href="https://x.com/uptexcoco" target="_blank">
                <img src="/img/twitter.png" alt="Twitter" />
              </a>
              Twitter
            </li>
            <li>
              <a href="http://sicoe.uptex.edu.mx/" target="_blank">
                <img src="/img/sicoe.png" alt="SICOE" />
              </a>
              SICOE
            </li>
          </ul>
        </div>

        <!-- Lado Derecho: Imagen de la Universidad -->
        <div class="contacto-imagen">
          <a href="https://uptexcoco.edomex.gob.mx/" target="_blank">
            <img src="/img/uptex1.jpeg" alt="Universidad Politécnica de Texcoco" />
          </a>
          <p>Universidad Politécnica de Texcoco</p>
        </div>
      </section>
    </div>

    <Exterior pie />
  </div>
</template>

<script setup>
import Exterior from '@/components/Exterior.vue'
</script>

<style src="@/../css/global.css"></style>
