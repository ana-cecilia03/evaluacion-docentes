<template>
  <!-- Modal de registro manual de alumno -->
  <div class="modal-overlay">
    <div class="modal-content">
      <div class="register-container">
        <h1 class="titulo">Actualizar</h1>

        <form @submit.prevent="actualizarCarrera" class="register-form">
          <!-- Campo: CLAVE -->
          <div class="form-group">
            <label for="clave">Clave</label>
            <input type="text" id="clave" v-model="carrera.clave" placeholder="Ej. MSC4 "/>
          </div>

          <!-- Campo: nombre -->
          <div class="form-group">
            <label for="matricula">Nombre</label>
            <input type="text" id="nombre" v-model="carrera.nombre" placeholder="Ingenieria Robotica" />
          </div>

          <!-- Botones -->
          <div class="button-group">
            <button type="button" class="register-rojo" @click="$emit('cerrar')">Cancelar</button>
            <button type="submit" class="register-verde">Actualizar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

// Datos del alumno (se enviarán al backend más adelante)
const carrera = ref({
  clave: '',
  nombre: ''
})

// Función temporal de prueba (será reemplazada con llamada a backend)
const actualizarCarrera = () => {
  // Aquí se conectará con Laravel/Inertia vía POST
  console.log('Carrera a registrar:', carrera.value)
}
</script>

<style src="@/../css/EditarDatos.css"></style>

