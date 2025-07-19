<template>
  <!-- Modal para registrar asignatura manualmente -->
  <div class="modal-overlay">
    <div class="modal-content">
      <div class="register-container">
        <h1 class="titulo" >Actualizar</h1>

          <!-- Campo: clave -->
          <div class="form-group">
            <label for="clave">Clave</label>
            <input type="text" id="clave" v-model.clave="materia.clave" placeholder="Ej. VSC1" />
          </div>
        
        <form @submit.prevent="registrarAsignatura" class="register-form">
          <!-- Campo: Nombre de la asignatura -->
          <div class="form-group">
            <label for="nombre">Nombre de la Materia</label>
            <input type="text" id="materia" v-model="materia.nombre" placeholder="Ej. Álgebra Lineal" />
          </div>

          <!-- Botones de acción -->
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

// Estado local del formulario de asignatura
const materia = ref({
  clave: '',
  materia: ''
})

// Esta función será reemplazada luego por lógica de backend (API)

</script>

<!-- Estilos específicos del formulario de registro -->
<style src="@/../css/EditarDatos.css"></style>
