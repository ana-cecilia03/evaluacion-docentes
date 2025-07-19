<template>
  <!-- Modal de registro manual de alumno -->
  <div class="modal-overlay">
    <div class="modal-content">
      <div class="register-container">
        <h1 class="titulo">Registrar Alumno Manualmente</h1>

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

          <!-- Campo: CURP -->
          <div class="form-group">
            <label for="curp">Curp</label>
            <input type="text" id="curp" v-model="alumno.curp" placeholder="VG6F6FGDX4YGMSC2" />
          </div>

          <!-- Campo: Grupo -->
          <div class="form-group">
            <label for="grupo">Grupo</label>
            <input type="text" id="grupo" v-model="alumno.grupo" placeholder="Grupo A" />
          </div>

          <!-- Campo: Estado -->
          <div class="form-group">
            <label for="estado">Estado</label>
            <select id="estado" v-model="alumno.estado">
              <option value="">Seleccione</option>
              <option>Activo</option>
              <option>Inactivo</option>
            </select>
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
  curp: '' ,
  grupo: '',
  estado: ''
})

// Función temporal de prueba (será reemplazada con llamada a backend)
const registrarAlumno = () => {
  // Aquí se conectará con Laravel/Inertia vía POST
  console.log('Alumno a registrar:', alumno.value)
}
</script>

<style src="@/../css/RegistroManual.css"></style>

