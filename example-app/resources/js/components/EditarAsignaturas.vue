<template>
  <!-- Modal para editar una materia -->
  <div class="modal-overlay">
    <div class="modal-content">
      <div class="register-container">
        <h1 class="titulo">Actualizar Materia</h1>

        <form @submit.prevent="actualizarMateria" class="register-form">
          <!-- Campo: Clave de la materia -->
          <div class="form-group">
            <label for="clave">Clave</label>
            <input
              type="text"
              id="clave"
              v-model="formulario.clave"
              placeholder="Ej. MAT101"
              required
            />
          </div>

          <!-- Campo: Nombre de la materia -->
          <div class="form-group">
            <label for="nombre_materia">Nombre de la Materia</label>
            <input
              type="text"
              id="nombre_materia"
              v-model="formulario.nombre_materia"
              placeholder="Ej. Álgebra Lineal"
              required
            />
          </div>

          <!-- Botones de acción -->
          <div class="button-group">
            <button type="button" class="register-rojo" @click="$emit('cerrar')">Cancelar</button>
            <button type="submit" class="register-verde">Actualizar</button>
          </div>

          <!-- Mensaje de error -->
          <div v-if="error" class="error-message">{{ error }}</div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

// Props
const props = defineProps({
  materia: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['cerrar', 'guardado'])

const formulario = ref({
  clave: '',
  nombre_materia: ''
})

const error = ref(null)

// Inicializar datos al montar
onMounted(() => {
  if (!props.materia || !props.materia.id_materia) {
    error.value = 'ID de materia no válido.'
    return
  }

  formulario.value = {
    clave: props.materia.clave,
    nombre_materia: props.materia.nombre_materia
  }
})

// Función para actualizar
const actualizarMateria = async () => {
  error.value = null

  try {
    await axios.put(`/api/materias/${props.materia.id_materia}`, {
      ...formulario.value,
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
      error.value = 'Error al actualizar la materia.'
    }
  }
}
</script>

