<template>
  <!-- Modal de edición de grupo -->
  <div class="modal-overlay">
    <div class="modal-content">
      <div class="register-container">
        <h1 class="titulo">Actualizar Grupo</h1>

        <!-- Formulario de edición -->
        <form @submit.prevent="actualizarGrupo" class="register-form">
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

const props = defineProps({
  grupo: Object // Se espera { id_grupo, clave, carrera }
})

const emit = defineEmits(['cerrar', 'actualizado'])

const form = ref({ ...props.grupo })
const error = ref(null)

const actualizarGrupo = async () => {
  error.value = null
  try {
    await axios.put(`/api/grupos/${form.value.id_grupo}`, {
      clave: form.value.clave,
      carrera: form.value.carrera,
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

// Si las props cambian, actualiza el formulario
watch(() => props.grupo, (nuevo) => {
  form.value = { ...nuevo }
})
</script>

<style src="@/../css/EditarDatos.css"></style>
