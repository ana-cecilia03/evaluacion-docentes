<template>
  <!-- Modal para registrar asignatura manualmente -->
  <div class="modal-overlay">
    <div class="modal-content">
      <div class="register-container">
        <h1 class="titulo">Registrar Materia Manualmente</h1>

        <form @submit.prevent="registrarAsignatura" class="register-form">
          <!-- Campo: Nombre de la materia -->
          <div class="form-group">
            <label for="nombre_materia">Nombre de la Materia</label>
            <input
              type="text"
              id="nombre_materia"
              v-model="materia.nombre_materia"
              placeholder="Ej. Álgebra Lineal"
              required
            />
          </div>

          <!-- Botones de acción -->
          <div class="button-group">
            <button type="button" class="register-rojo" @click="$emit('cerrar')">Cancelar</button>
            <button type="submit" class="register-verde">Registrar Materia</button>
          </div>

          <!-- Mensaje de error -->
          <div v-if="error" class="error-message">{{ error }}</div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'

const emit = defineEmits(['cerrar', 'guardado'])

const materia = ref({
  nombre_materia: ''
})

const error = ref(null)

const registrarAsignatura = async () => {
  error.value = null

  try {
    await axios.post('/api/materias', {
      ...materia.value,
      created_by: 'frontend',
      modified_by: 'frontend'
    })

    emit('guardado')
    emit('cerrar')
  } catch (err) {
    console.error(err)
    if (err.response?.data?.errors) {
      const errores = Object.values(err.response.data.errors).flat()
      error.value = errores.join(', ')
    } else {
      error.value = 'Error al registrar la materia.'
    }
  }
}
</script>

<!-- Estilos específicos del formulario de registro -->
<style src="@/../css/RegistroManual.css"></style>
