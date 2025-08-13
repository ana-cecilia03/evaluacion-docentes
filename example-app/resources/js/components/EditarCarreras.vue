<template>
  <!-- Modal de actualización de carrera -->
  <div class="modal-overlay">
    <div class="modal-content">
      <div class="register-container">
        <h1 class="titulo">Actualizar</h1>

        <form @submit.prevent="actualizarCarrera" class="register-form">
          <!-- Campo: CLAVE -->
          <div class="form-group">
            <label for="clave">Clave</label>
            <input
              type="text"
              id="clave"
              v-model="carreraLocal.clave"
              placeholder="Ej. MSC4"
            />
          </div>

          <!-- Campo: Nombre -->
          <div class="form-group">
            <label for="nombre">Nombre</label>
            <input
              type="text"
              id="nombre"
              v-model="carreraLocal.nombre_carrera"
              placeholder="Ingeniería Robótica"
            />
          </div>

          <!-- Botones -->
          <div class="button-group">
            <button type="button" class="register-rojo" @click="$emit('cerrar')">
              Cancelar
            </button>
            <button type="submit" class="register-verde">Actualizar</button>
          </div>

          <!-- Mostrar errores -->
          <div v-if="error" class="error-message">
            {{ error }}
          </div>
        </form>
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
  carrera: {
    type: Object,
    required: true
  }
})

// Estado local para edición
const carreraLocal = ref({
  id_carrera: null,
  clave: '',
  nombre_carrera: ''
})

// Error personalizado
const error = ref('')

// Cuando cambie la prop, actualizamos localmente
watch(
  () => props.carrera,
  (nueva) => {
    if (nueva) {
      carreraLocal.value = { ...nueva }
      error.value = '' // limpiar errores anteriores
    }
  },
  { immediate: true }
)

// Lógica de actualización con manejo de errores
const actualizarCarrera = async () => {
  error.value = ''
  try {
    await axios.put(`/api/carreras/${carreraLocal.value.id_carrera}`, {
      clave: carreraLocal.value.clave,
      nombre_carrera: carreraLocal.value.nombre_carrera,
      modified_by: 'frontend'
    })
    emit('actualizado')
    emit('cerrar')
  } catch (err) {
    if (err.response && err.response.data && err.response.data.errors) {
      if (err.response.data.errors.clave) {
        error.value = err.response.data.errors.clave[0] // muestra "Esa clave ya está registrada."
      } else {
        error.value = 'Error al actualizar la carrera.'
      }
    } else {
      error.value = '❌ No se puede completar la acción porque esta materia está relacionada con otros registros.'
    }
    console.error('Error al actualizar carrera:', err)
  }
}
</script>

