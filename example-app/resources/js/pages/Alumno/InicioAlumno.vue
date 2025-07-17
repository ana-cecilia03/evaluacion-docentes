<template>
  <MenuAlumno>
    <div class="seleccion-contenedor">
      <h1>Bienvenido al sistema de evaluación docentes</h1>

      <div class="seleccion-formulario">
        <div class="campo">
          <label>Selecciona tu carrera</label>
          <select>
            <option disabled selected value="">Carrera</option>
            <option>Ingeniería en Sistemas</option>
            <option>Ingeniería Civil</option>
            <option>Arquitectura</option>
          </select>
        </div>

        <div class="campo">
          <label>Selecciona tu grupo</label>
          <select>
            <option disabled selected value="">Grupo</option>
            <option>Grupo 101</option>
            <option>Grupo 202</option>
            <option>Grupo 303</option>
          </select>
        </div>
      </div>
    </div>

    <div class="contenedor">
      <button class="boton-aceptar">Aceptar</button>
    </div>
  </MenuAlumno>
</template>

<script setup>
// Script vacío por ahora
import MenuAlumno from '@/layouts/MenuAlumno.vue'
</script>

<!-- Importar estilos externos -->
<style src="@/../css/Externo.css"></style>
<style src="@/../css/Inicio.css"></style>
