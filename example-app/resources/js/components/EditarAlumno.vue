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
            <input
              type="text"
              id="nombre"
              v-model="alumnoLocal.nombre_completo"
              placeholder="Ej. Ana López"
            />
          </div>

          <!-- Campo: Matrícula -->
          <div class="form-group">
            <label for="matricula">Matrícula</label>
            <input
              type="text"
              id="matricula"
              v-model="alumnoLocal.matricula"
              placeholder="Ej. 20230101"
            />
          </div>

          <!-- Campo: Correo -->
          <div class="form-group">
            <label for="correo">Correo electrónico</label>
            <input
              type="email"
              id="correo"
              v-model="alumnoLocal.correo"
              placeholder="correo@ejemplo.com"
            />
          </div>

          <!-- Campo: Contraseña -->
          <div class="form-group">
            <label for="password">Contraseña</label>
            <input
              type="password"
              id="password"
              v-model="alumnoLocal.password"
              placeholder="Nueva contraseña o deje vacío para no cambiarla"
            />
          </div>

          <!-- Campo: Grupo -->
          <div class="form-group">
            <label for="grupo">Grupo (ID numérico)</label>
            <input
              type="number"
              id="grupo"
              v-model.number="alumnoLocal.grupo"
              placeholder="Ej. 1"
            />
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

// Estado local para edición
const alumnoLocal = ref({
  id_alumno: null,
  nombre_completo: '',
  matricula: '',
  correo: '',
  password: '',
  grupo: null,
  status: ''
})

// Copiar datos de la prop a la copia local al cargarse o actualizarse
watch(
  () => props.alumno,
  (nuevo) => {
    if (nuevo) {
      alumnoLocal.value = {
        ...nuevo,
        password: '' // no mostramos la contraseña actual por seguridad
      }
    }
  },
  { immediate: true }
)

const error = ref(null)

// Función para actualizar el alumno
const actualizarAlumno = async () => {
  error.value = null

  try {
    const payload = {
      nombre_completo: alumnoLocal.value.nombre_completo,
      matricula: alumnoLocal.value.matricula,
      correo: alumnoLocal.value.correo,
      grupo: alumnoLocal.value.grupo,
      status: alumnoLocal.value.status,
      modified_by: 'frontend'
    }

    // Solo enviamos la nueva contraseña si fue escrita
    if (alumnoLocal.value.password && alumnoLocal.value.password.trim() !== '') {
      payload.password = alumnoLocal.value.password
    }

    await axios.put(`/api/alumnos/${alumnoLocal.value.id_alumno}`, payload)

    emit('actualizado')
    emit('cerrar')
  } catch (err) {
    error.value = err.response?.data?.message || 'Error al actualizar alumno.'
    console.error('Error al actualizar alumno:', err)
  }
}
</script>

<style src="@/../css/EditarDatos.css"></style>
