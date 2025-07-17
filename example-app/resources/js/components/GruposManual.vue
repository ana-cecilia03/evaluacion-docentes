<template>
  <!-- Modal de registro de grupo manual -->
  <div class="modal-overlay">
    <div class="modal-content">
      <div class="register-container">
        <h1>Registrar Grupo Manualmente</h1>

        <!-- Formulario de registro -->
        <form @submit.prevent="registrarGrupo" class="register-form">
          <!-- Campo: nombre del grupo -->
          <div class="form-group">
            <label for="nombre">Nombre del Grupo</label>
            <input
              type="text"
              id="nombre"
              v-model="grupo.nombre"
              placeholder="Ej. Grupo 3B"
            />
          </div>

          <!-- Campo: asignatura -->
          <div class="form-group">
            <label for="asignatura">Asignatura</label>
            <select id="asignatura" v-model="grupo.asignatura">
              <option value="">Seleccione</option>
              <option>Matemáticas</option>
              <option>Física</option>
            </select>
          </div>

          <!-- Campo: profesor -->
          <div class="form-group">
            <label for="profesor">Profesor</label>
            <select id="profesor" v-model="grupo.profesor">
              <option value="">Seleccione</option>
              <option>Laura Martínez</option>
              <option>José Herrera</option>
            </select>
          </div>

          <!-- Botones de acción -->
          <div class="button-group">
            <button type="button" class="register-rojo" @click="$emit('cerrar')">Cancelar</button>
            <button type="submit" class="register-verde">Registrar Grupo</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

// Estado del grupo a registrar
const grupo = ref({
  nombre: '',
  asignatura: '',
  profesor: ''
})

// Acción al hacer clic en "Registrar Grupo"
const registrarGrupo = () => {
  // Aquí se conectará al backend cuando esté listo
  console.log('Datos del grupo:', grupo.value)
  alert('Grupo registrado correctamente')
}
</script>

<!-- Estilos específicos para el formulario de registro manual -->
<style src="@/../css/RegistroManual.css"></style>
