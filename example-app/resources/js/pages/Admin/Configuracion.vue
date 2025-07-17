<template>
  <Menu>
    <div class="contenido-principal">
      <h1 class="titulo">Configuración</h1>

      <div class="config-box">
        <label>Nombre:</label>
        <input type="text" placeholder="Nombre" />

        <label>Apellido:</label>
        <input type="text" placeholder="Apellido" />

        <label>Correo electrónico:</label>
        <input type="email" placeholder="correo@electronico.com" />

        <label>Contraseña:</label>
        <div>
          <input type="password" value="********" readonly />
        </div>

        <button class="boton-actualizar">Actualizar</button>
      </div>
    </div>
  </Menu>
</template>

<script setup>
import Menu from '@/layouts/Menu.vue'
// script vacío por ahora, backend se conectará después
</script>

<style src="@/../css/Contenido.css"></style>
<style src="@/../css/botones.css"></style>
