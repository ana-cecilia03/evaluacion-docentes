<template> 
  <div class="modal-overlay">
    <div class="modal-content">
      <div class="register-container">
        <h1 class="titulo">Registrar Profesor Manualmente</h1>

        <form @submit.prevent="registrarProfesor" class="register-form">
          <!-- Fila de datos principales -->
          <div class="form-row">
            <!-- Nombre completo -->
            <div class="form-group">
              <label for="nombre_completo">Nombre completo</label>
              <input
                type="text"
                id="nombre_completo"
                v-model="form.nombre_completo"
                placeholder="Ej. Laura Martínez"
              />
            </div>

            <!-- Matrícula -->
            <div class="form-group">
              <label for="matricula">Matrícula</label>
              <input
                type="text"
                id="matricula"
                v-model="form.matricula"
                placeholder="Ej. 13122593103"
                maxlength="11"
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
                v-model="form.correo"
                placeholder="Ej. profesor@ejemplo.com"
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
              />
            </div>

            <!-- Estado -->
            <div class="form-group">
              <label for="status">Estado</label>
              <select id="status" v-model="form.status">
                <option value="">Seleccione</option>
                <option value="activo">Activo</option>
                <option value="inactivo">Inactivo</option>
              </select>
            </div>
          </div>

          <!-- Botones -->
          <div class="button-group">
            <button type="button" class="register-rojo" @click="$emit('cerrar')">Cancelar</button>
            <button type="submit" class="register-verde">Registrar Profesor</button>
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
  matricula: '',
  nombre_completo: '',
  correo: '',
  password: '',
  status: ''
})

const error = ref(null)

const registrarProfesor = async () => {
  error.value = null

  try {
    await axios.post('/api/profesores', {
      ...form.value,
      rol: 'profesor',
      created_by: 'frontend',
      modified_by: 'frontend'
    })

    emit('guardado')
    emit('cerrar')
  } catch (err) {
    console.error('Error al registrar profesor:', err)
    if (err.response?.data?.errors) {
      const errores = Object.values(err.response.data.errors).flat()
      error.value = errores.join(', ')
    } else {
      error.value = 'Error al registrar profesor.'
    }
  }
}
</script>

<style src="@/../css/RegistroManual.css"></style>
