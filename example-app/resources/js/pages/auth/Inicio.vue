<template>
  <div class="bienvenida-login-page">
    <Exterior />

    <div class="inicio-container">
      <h1 class="titulo">Bienvenido al sistema de evaluación docentes</h1>
      <div class="imagen-wrapper">
        <img src="/img/carreras.png" />
      </div>
    </div>

    <Exterior pie />
  </div>
</template>

<script setup>
import Exterior from '@/components/Exterior.vue'
</script>
<style src="@/../css/bienvenida.css"></style>