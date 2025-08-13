<template>
  <!-- Modal de edición de grupo -->
  <div class="modal-overlay">
    <div class="modal-content">
      <div class="register-container">
        <h1 class="titulo">Actualizar Grupo</h1>

        <!-- Formulario de edición -->
        <form @submit.prevent="actualizarGrupo" class="register-form">
          <!-- Campo: nombre -->
          <div class="form-group">
            <label for="nombre">Nombre del Grupo</label>
            <input
              type="text"
              id="nombre"
              v-model="form.nombre"
              placeholder="Ej. MSC8"
              required
            />
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
  grupo: {
    type: Object,
    required: true // { id_grupo, nombre }
  }
})

const emit = defineEmits(['cerrar', 'actualizado'])

const form = ref({
  id_grupo: null,
  nombre: ''
})

const error = ref(null)

// Sincronizar props iniciales
watch(() => props.grupo, (nuevo) => {
  if (nuevo && typeof nuevo === 'object') {
    form.value = {
      id_grupo: nuevo.id_grupo ?? null,
      nombre: nuevo.nombre ?? ''
    }
  }
}, { immediate: true })

// Enviar la actualización
const actualizarGrupo = async () => {
  error.value = null
  try {
    await axios.put(`/api/grupos/${form.value.id_grupo}`, {
      nombre: form.value.nombre,
      modified_by: 'frontend'
    })
    emit('actualizado')
    emit('cerrar')
  } catch (err) {
    console.error('Error al actualizar grupo:', err)
    if (err.response?.data?.errors) {
      const errores = Object.values(err.response.data.errors).flat()
      error.value = errores.join(', ')
    } else {
      error.value = 'Error al actualizar grupo.'
    }
  }
}
</script>

<style src="@css/global.css" lang="css"></style>
