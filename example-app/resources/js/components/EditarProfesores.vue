<template>
  <!-- Modal para editar profesor -->
  <div class="modal-overlay">
    <div class="modal-content">
      <div class="register-container">
        <h1 class="titulo">Actualizar Profesor</h1>

        <form @submit.prevent="actualizarProfesor" class="register-form">
          <!-- Campo: Matrícula -->
          <div class="form-group">
            <label for="matricula">Matrícula</label>
            <input type="text" id="matricula" v-model="form.matricula" placeholder="Ej. 13122593103" />
          </div>

          <!-- Campo: Nombre completo -->
          <div class="form-group">
            <label for="nombre_completo">Nombre completo</label>
            <input type="text" id="nombre_completo" v-model="form.nombre_completo" placeholder="Ej. Laura Martínez" />
          </div>

          <!-- Campo: Correo -->
          <div class="form-group">
            <label for="correo">Correo electrónico</label>
            <input type="email" id="correo" v-model="form.correo" placeholder="Ej. profesor@ejemplo.com" />
          </div>

          <!-- Campo: CURP -->
          <div class="form-group">
            <label for="curp">CURP</label>
            <input type="text" id="curp" v-model="form.curp" placeholder="MDFI32URAS9RMVS7" />
          </div>

          <!-- Campo: Estado -->
          <div class="form-group">
            <label for="status">Estado</label>
            <select id="status" v-model="form.status">
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

        <!-- Mensaje de error -->
        <div v-if="error" class="error-message">
          {{ error }}
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, defineProps, defineEmits } from 'vue'
import axios from 'axios'

// Props
const props = defineProps({
  profesor: {
    type: Object,
    required: true
  }
})

// Emits
const emit = defineEmits(['cerrar', 'actualizado'])

// Formulario editable
const form = ref({
  matricula: '',
  nombre_completo: '',
  correo: '',
  curp: '',
  status: ''
})

// Cargar datos iniciales cuando cambia la prop
watch(() => props.profesor, (nuevo) => {
  if (nuevo) {
    form.value = { ...nuevo }
  }
}, { immediate: true })

const error = ref(null)

// Enviar actualización al backend
const actualizarProfesor = async () => {
  error.value = null

  try {
    await axios.put(`/api/profesores/${form.value.id_profesor}`, {
      matricula: form.value.matricula,
      nombre_completo: form.value.nombre_completo,
      correo: form.value.correo,
      curp: form.value.curp,
      status: form.value.status,
      modified_by: 'frontend'
    })

    emit('actualizado')
    emit('cerrar')
  } catch (err) {
    console.error('Error al actualizar profesor:', err)
    if (err.response?.data?.message) {
      error.value = err.response.data.message
    } else if (err.response?.data?.errors) {
      const errores = Object.values(err.response.data.errors).flat()
      error.value = errores.join(', ')
    } else {
      error.value = 'Error inesperado al actualizar profesor.'
    }
  }
}
</script>

<style src="@/../css/RegistroManual.css"></style>
