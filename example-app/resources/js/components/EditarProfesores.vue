<template>
  <!-- Modal para editar profesor -->
  <div class="modal-overlay">
    <div class="modal-content">
      <div class="register-container">
        <h1 class="titulo">Actualizar Profesor</h1>

        <form @submit.prevent="actualizarProfesor" class="register-form">
          <!-- Matrícula -->
          <div class="form-group">
            <label for="matricula">Matrícula</label>
            <input
              type="text"
              id="matricula"
              v-model="form.matricula"
              placeholder="Ej. 13122593103"
              maxlength="11"
              required
            />
          </div>

          <!-- Nombre completo -->
          <div class="form-group">
            <label for="nombre_completo">Nombre completo</label>
            <input
              type="text"
              id="nombre_completo"
              v-model="form.nombre_completo"
              placeholder="Ej. Laura Martínez"
              maxlength="100"
              required
            />
          </div>

          <!-- Correo -->
          <div class="form-group">
            <label for="correo">Correo electrónico</label>
            <input
              type="email"
              id="correo"
              v-model="form.correo"
              placeholder="Ej. profesor@ejemplo.com"
              maxlength="50"
              required
            />
          </div>

          <!-- Contraseña (opcional) -->
          <div class="form-group">
            <label for="password">Contraseña (opcional)</label>
            <input
              type="password"
              id="password"
              v-model="form.password"
              placeholder="********"
              minlength="6"
            />
          </div>

          <!-- Cargo -->
          <div class="form-group">
            <label for="cargo">Cargo</label>
            <select id="cargo" v-model="form.cargo" required>
              <option value="">Seleccione</option>
              <option value="PA">PA</option>
              <option value="PTC">PTC</option>
            </select>
          </div>

          <!-- Estado -->
          <div class="form-group">
            <label for="status">Estado</label>
            <select id="status" v-model="form.status" required>
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

// Eventos
const emit = defineEmits(['cerrar', 'actualizado'])

// Estado del formulario
const form = ref({
  id_profesor: null,
  matricula: '',
  nombre_completo: '',
  correo: '',
  password: '',
  cargo: '',
  status: ''
})

// Log inicial del prop recibido
console.log('Prop profesor recibido:', props.profesor)

// Sincronizar props iniciales
watch(() => props.profesor, (nuevo) => {
  if (nuevo && typeof nuevo === 'object') {
    form.value = {
      id_profesor: nuevo.id_profesor ?? null,
      matricula: nuevo.matricula ?? '',
      nombre_completo: nuevo.nombre_completo ?? '',
      correo: nuevo.correo ?? '',
      password: '',
      cargo: nuevo.cargo ?? '',
      status: nuevo.status ?? ''
    }
    console.log('Formulario sincronizado con props:', form.value)
  }
}, { immediate: true })

const error = ref(null)

// Enviar actualización
const actualizarProfesor = async () => {
  error.value = null
  console.log('Iniciando actualización del profesor...')

  const payload = {
    matricula: form.value.matricula,
    nombre_completo: form.value.nombre_completo,
    correo: form.value.correo,
    cargo: form.value.cargo,
    status: form.value.status,
    modified_by: 'frontend'
  }

  if (form.value.password?.trim()) {
    payload.password = form.value.password
  }

  console.log('Payload a enviar al backend:', payload)

  try {
    const response = await axios.put(`/api/profesores/${form.value.id_profesor}`, payload)
    console.log('Respuesta exitosa del backend:', response.data)

    emit('actualizado')
    emit('cerrar')
  } catch (err) {
    console.error('Error al actualizar profesor (catch):', err)

    if (err.response?.data?.errors) {
      const errores = Object.values(err.response.data.errors).flat()
      error.value = errores.join(', ')
      console.error('Errores de validación:', error.value)
    } else if (err.response?.data?.message) {
      error.value = err.response.data.message
      console.error('Mensaje de error:', error.value)
    } else {
      error.value = 'Error inesperado al actualizar profesor.'
    }
  }
}
</script>

<style src="@/../css/RegistroManual.css"></style>
