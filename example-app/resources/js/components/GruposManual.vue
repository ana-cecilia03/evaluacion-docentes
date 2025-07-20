<template>
  <!-- Modal de registro de grupo manual -->
  <div class="modal-overlay">
    <div class="modal-content">
      <div class="register-container">
        <h1 class="titulo">Registrar Grupo Manualmente</h1>

        <!-- Formulario de registro -->
        <form @submit.prevent="registrarGrupo" class="register-form">
          <!-- Campo: clave -->
          <div class="form-group">
            <label for="clave">Clave</label>
            <input type="text" id="clave" v-model="form.clave" placeholder="Ej. MSC8" />
          </div>

          <!-- Campo: carrera -->
          <div class="form-group">
            <label for="carrera">Carrera</label>
            <input type="text" id="carrera" v-model="form.carrera" placeholder="Ej. Ingeniería en Robótica" />
          </div>

          <!-- Botones de acción -->
          <div class="button-group">
            <button type="button" class="register-rojo" @click="$emit('cerrar')">Cancelar</button>
            <button type="submit" class="register-verde">Registrar Grupo</button>
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
import { ref } from 'vue'
import axios from 'axios'

const emit = defineEmits(['cerrar', 'guardado'])

const form = ref({
  clave: '',
  carrera: ''
})

const error = ref(null)

const registrarGrupo = async () => {
  error.value = null
  try {
    await axios.post('/api/grupos', {
      ...form.value,
      created_by: 'frontend',
      modified_by: 'frontend'
    })
    emit('guardado')
    emit('cerrar')
  } catch (err) {
    console.error('Error al registrar grupo:', err)
    if (err.response?.data?.errors) {
      const errores = Object.values(err.response.data.errors).flat()
      error.value = errores.join(', ')
    } else {
      error.value = 'Error al registrar grupo.'
    }
  }
}
</script>

<style src="@/../css/RegistroManual.css"></style>
