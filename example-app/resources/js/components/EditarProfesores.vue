<template>
  <!-- Modal para editar profesor -->
  <div class="modal-overlay">
    <div class="modal-content">
      <div class="register-container">
        <h1 class="titulo">Actualizar Profesor</h1>

        <!-- Formulario de edición -->
        <form @submit.prevent="actualizarProfesor" class="register-form" novalidate>
          <!-- Matrícula -->
          <div class="form-group">
            <label for="matricula">Matrícula</label>
            <input
              type="text"
              id="matricula"
              v-model.trim="form.matricula"
              placeholder="Ej. 13122593103"
              maxlength="11"
              required
              autocomplete="off"
            />
          </div>

          <!-- Nombre completo -->
          <div class="form-group">
            <label for="nombre_completo">Nombre completo</label>
            <input
              type="text"
              id="nombre_completo"
              v-model.trim="form.nombre_completo"
              placeholder="Ej. Laura Martínez"
              maxlength="100"
              required
              autocomplete="off"
            />
          </div>

          <!-- Correo -->
          <div class="form-group">
            <label for="correo">Correo electrónico</label>
            <input
              type="email"
              id="correo"
              v-model.trim="form.correo"
              placeholder="Ej. profesor@ejemplo.com"
              maxlength="50"
              required
              autocomplete="off"
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
              autocomplete="new-password"
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

          <!-- Rol -->
          <div class="form-group">
            <label for="rol">Rol</label>
            <select id="rol" v-model="form.rol" required>
              <option value="">Seleccione</option>
              <option value="profesor">Profesor</option>
              <option value="administrador">Administrador</option>
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
            <button type="button" class="register-rojo" @click="$emit('cerrar')" :disabled="cargando">
              Cancelar
            </button>
            <button type="submit" class="register-verde" :disabled="cargando">
              {{ cargando ? 'Actualizando...' : 'Actualizar' }}
            </button>
          </div>
        </form>

        <!-- Errores -->
        <div v-if="error" class="error-message">
          <ul>
            <li v-for="(msg, i) in mensajesError" :key="i">{{ msg }}</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
// Modal de edición de profesor.
// Incluye campo "rol" y lo envía al backend junto con el resto de datos.

import { ref, watch, defineProps, defineEmits, computed } from 'vue'
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
  password: '', // opcional
  cargo: '',
  status: '',
  rol: '' // se sincroniza desde props
})

const error = ref(null)
const cargando = ref(false)

// Normaliza mensajes de error a una lista
const mensajesError = computed(() => {
  if (!error.value) return []
  if (Array.isArray(error.value)) return error.value
  if (typeof error.value === 'string') return [error.value]
  return [JSON.stringify(error.value)]
})

// Sincroniza el formulario cuando cambia la prop "profesor"
watch(
  () => props.profesor,
  (nuevo) => {
    if (nuevo && typeof nuevo === 'object') {
      form.value = {
        id_profesor: nuevo.id_profesor ?? null,
        matricula: nuevo.matricula ?? '',
        nombre_completo: nuevo.nombre_completo ?? '',
        correo: nuevo.correo ?? '',
        password: '',
        cargo: nuevo.cargo ?? '',
        status: nuevo.status ?? '',
        rol: nuevo.rol ?? 'profesor'
      }
    }
  },
  { immediate: true }
)

// Enviar actualización
const actualizarProfesor = async () => {
  error.value = null
  cargando.value = true

  // Construir payload; password solo si viene
  const payload = {
    matricula: form.value.matricula,
    nombre_completo: form.value.nombre_completo,
    correo: form.value.correo,
    cargo: form.value.cargo,
    status: form.value.status,
    rol: form.value.rol || 'profesor',
    modified_by: 'frontend'
  }

  if (form.value.password?.trim()) {
    payload.password = form.value.password
  }

  try {
    await axios.put(`/api/profesores/${form.value.id_profesor}`, payload)
    emit('actualizado')
    emit('cerrar')
  } catch (err) {
    if (err.response?.data?.errors) {
      const errores = Object.values(err.response.data.errors).flat()
      error.value = errores.length ? errores : 'Error inesperado al actualizar profesor.'
    } else if (err.response?.data?.message) {
      error.value = err.response.data.message
    } else {
      error.value = 'Error inesperado al actualizar profesor.'
    }
    console.error('Error al actualizar profesor:', err)
  } finally {
    cargando.value = false
  }
}
</script>

<style src="@/../css/RegistroManual.css"></style>
