<template>
  <div class="modal-overlay">
    <div class="modal-content">
      <div class="register-container">
        <h1 class="titulo">Registrar Profesor Manualmente</h1>

        <!-- Formulario de registro -->
        <form @submit.prevent="registrarProfesor" class="register-form" novalidate>
          <!-- Fila de datos principales -->
          <div class="form-row">
            <!-- Nombre completo -->
            <div class="form-group">
              <label for="nombre_completo">Nombre completo</label>
              <input
                type="text"
                id="nombre_completo"
                v-model.trim="form.nombre_completo"
                placeholder="Ej. Laura Martínez"
                required
                autocomplete="off"
              />
            </div>

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
          </div>

          <!-- Fila de datos secundarios -->
          <div class="form-row">
            <!-- Correo -->
            <div class="form-group">
              <label for="correo">Correo electrónico</label>
              <input
                type="email"
                id="correo"
                v-model.trim="form.correo"
                placeholder="Ej. profesor@ejemplo.com"
                required
                autocomplete="off"
              />
            </div>

            <!-- Contraseña -->
            <div class="form-group">
              <label for="password">Contraseña</label>
              <input
                type="password"
                id="password"
                v-model="form.password"
                placeholder="********"
                required
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
          </div>

          <!-- Botones -->
          <div class="button-group">
            <button type="button" class="register-rojo" @click="$emit('cerrar')" :disabled="cargando">
              Cancelar
            </button>
            <button type="submit" class="register-verde" :disabled="cargando">
              {{ cargando ? 'Registrando...' : 'Registrar Profesor' }}
            </button>
          </div>
        </form>

        <!-- Mensajes -->
        <div v-if="error" class="error-message">
          <ul>
            <li v-for="(msg, i) in mensajesError" :key="i">{{ msg }}</li>
          </ul>
        </div>
        <div v-if="exito" class="success-message">
          {{ exito }}
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
// Componente de registro manual de profesores.
// Envía todos los campos incluyendo "rol" sin sobrescribirlo en el payload.

import { ref, computed } from 'vue'
import axios from 'axios'

const emit = defineEmits(['cerrar', 'guardado'])

const form = ref({
  matricula: '',
  nombre_completo: '',
  correo: '',
  password: '',
  cargo: '',
  status: '',
  rol: 'profesor' // Valor por defecto
})

const error = ref(null)
const exito = ref('')
const cargando = ref(false)

// Normaliza posibles estructuras de error del backend a una lista de strings.
const mensajesError = computed(() => {
  if (!error.value) return []
  if (Array.isArray(error.value)) return error.value
  if (typeof error.value === 'string') return [error.value]
  return [JSON.stringify(error.value)]
})

const resetForm = () => {
  form.value = {
    matricula: '',
    nombre_completo: '',
    correo: '',
    password: '',
    cargo: '',
    status: '',
    rol: 'profesor'
  }
}

const registrarProfesor = async () => {
  error.value = null
  exito.value = ''
  cargando.value = true

  try {
    const payload = {
      ...form.value,
      // Si por alguna razón no viene rol, aplica default antes de enviar.
      rol: form.value.rol || 'profesor',
      created_by: 'frontend',
      modified_by: 'frontend'
    }

    await axios.post('/api/profesores', payload)

    exito.value = 'Profesor registrado correctamente.'
    emit('guardado')
    resetForm()
    // Opcional: cerrar automáticamente tras éxito
    emit('cerrar')
  } catch (err) {
    // Intenta obtener mensajes de validación de Laravel
    if (err.response?.data?.errors) {
      const errores = Object.values(err.response.data.errors).flat()
      error.value = errores.length ? errores : 'Error al registrar profesor.'
    } else if (err.response?.data?.message) {
      error.value = err.response.data.message
    } else {
      error.value = 'Error al registrar profesor.'
    }
    console.error('Error al registrar profesor:', err)
  } finally {
    cargando.value = false
  }
}
</script>

<style src="@css/global.css" lang="css"></style>
