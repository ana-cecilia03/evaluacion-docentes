<template>
  <!-- Modal de registro manual de alumno -->
  <div class="modal-overlay">
    <div class="modal-content">
      <div class="register-container">
        <h1>Registrar Alumno Manualmente</h1>

        <form @submit.prevent="registrarAlumno" class="register-form">
          <!-- Campo: Nombre -->
          <div class="form-group">
            <label for="nombre">Nombre completo</label>
            <input type="text" id="nombre" v-model="alumno.nombre" placeholder="Ej. Ana López" />
          </div>

          <!-- Campo: Matrícula -->
          <div class="form-group">
            <label for="matricula">Matrícula</label>
            <input type="text" id="matricula" v-model="alumno.matricula" placeholder="Ej. 20230101" />
          </div>

          <!-- Campo: Grupo -->
          <div class="form-group">
            <label for="grupo">Grupo</label>
            <select id="grupo" v-model="alumno.grupo">
              <option value="">Seleccione</option>
              <option>Grupo A</option>
              <option>Grupo B</option>
              <option>Grupo C</option>
            </select>
          </div>

          <!-- Campo: Carrera -->
          <div class="form-group">
            <label for="carrera">Carrera</label>
            <input type="text" id="carrera" v-model="alumno.carrera" placeholder="Ej. Ingeniería en Sistemas" />
          </div>

          <!-- Botones -->
          <div class="button-group">
            <button type="button" class="register-rojo" @click="$emit('cerrar')">Cancelar</button>
            <button type="submit" class="register-verde">Registrar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

// Datos del alumno (se enviarán al backend más adelante)
const alumno = ref({
  nombre: '',
  matricula: '',
  grupo: '',
  carrera: ''
})

// Función temporal de prueba (será reemplazada con llamada a backend)
const registrarAlumno = () => {
  // Aquí se conectará con Laravel/Inertia vía POST
  console.log('Alumno a registrar:', alumno.value)
}
</script>

<style src="@/../css/RegistroManual.css"></style>

