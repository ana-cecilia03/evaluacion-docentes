<template>
  <!-- Modal de edición de alumno -->
  <div class="modal-overlay">
    <div class="modal-content">
      <div class="register-container">
        <h1 class="titulo">Actualizar Alumno</h1>

        <form @submit.prevent="actualizarAlumno" class="register-form">
          <!-- Campo: Nombre completo -->
          <div class="form-group">
            <label for="nombre">Nombre completo</label>
            <input type="text" id="nombre" v-model="alumnoLocal.nombre_completo" placeholder="Ej. Ana López" />
          </div>

          <!-- Campo: Matrícula -->
          <div class="form-group">
            <label for="matricula">Matrícula</label>
            <input type="text" id="matricula" v-model="alumnoLocal.matricula" placeholder="Ej. 20230101" />
          </div>

          <!-- Campo: CURP -->
          <div class="form-group">
            <label for="curp">CURP</label>
            <input type="text" id="curp" v-model="alumnoLocal.curp" placeholder="VG6F6FGDX4YGMSC2" />
          </div>

          <!-- Campo: Grupo -->
          <div class="form-group">
            <label for="grupo">Grupo</label>
            <input type="text" id="grupo" v-model="alumnoLocal.grupo" placeholder="Grupo A" />
          </div>

          <!-- Campo: Estado -->
          <div class="form-group">
            <label for="estado">Estado</label>
            <select id="estado" v-model="alumnoLocal.status">
              <option value="">Seleccione</option>
              <option value="activo">Activo</option>
              <option value="inactivo">Inactivo</option>
            </select>
          </div>

          <!-- Botones -->
          <div class="button-group">
            <button type="button" class="register-rojo" @click="$emit('cerrar')">Cancelar</button>
            <button type="submit" class="register-verde">Actualizar</button>
          </div>
        </form>

        <!-- Error -->
        <div v-if="error" class="error-message">
          {{ error }}
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import axios from 'axios'
import { defineProps, defineEmits } from 'vue'

const emit = defineEmits(['cerrar', 'actualizado'])

const props = defineProps({
  alumno: {
    type: Object,
    required: true
  }
})

// Crear una copia local para edición sin mutar la prop
const alumnoLocal = ref({
  id_alumno: null,
  nombre_completo: '',
  matricula: '',
  curp: '',
  grupo: '',
  status: ''
})

// Copiar datos cuando se actualiza la prop
watch(
  () => props.alumno,
  (nuevo) => {
    if (nuevo) {
      alumnoLocal.value = { ...nuevo }
    }
  },
  { immediate: true }
)

const error = ref(null)

// Función para enviar actualización al backend
const actualizarAlumno = async () => {
  error.value = null
  try {
    await axios.put(`/api/alumnos/${alumnoLocal.value.id_alumno}`, {
      ...alumnoLocal.value,
      modified_by: 'frontend'
    })
    emit('actualizado') // Recarga la tabla en la vista principal
    emit('cerrar')      // Cierra el modal
  } catch (err) {
    if (err.response?.data?.message) {
      error.value = err.response.data.message
    } else {
      error.value = 'Error al actualizar alumno.'
    }
    console.error('Error al actualizar alumno:', err)
  }
}
</script>

<style src="@/../css/EditarDatos.css"></style>
