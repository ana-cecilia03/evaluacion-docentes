<template>
  <!-- Modal de registro manual de alumno -->
  <div class="modal-overlay">
    <div class="modal-content">
      <div class="register-container">
        <h1 class="titulo">Registrar Alumno Manualmente</h1>

        <form @submit.prevent="registrarAlumno" class="register-form">
          <!-- Campo: Nombre -->
          <div class="form-group">
            <label for="nombre">Nombre completo</label>
            <input type="text" id="nombre" v-model="alumno.nombre_completo" placeholder="Ej. Ana López" />
          </div>

          <!-- Campo: Matrícula -->
          <div class="form-group">
            <label for="matricula">Matrícula</label>
            <input type="text" id="matricula" v-model="alumno.matricula" placeholder="Ej. 20230101" />
          </div>

          <!-- Campo: Contraseña -->
          <div class="form-group">
            <label for="password">Contraseña</label>
            <input type="password" id="password" v-model="alumno.password" placeholder="********" />
          </div>

          <!-- Campo: Grupo -->
          <div class="form-group">
            <label for="grupo">Grupo (número)</label>
            <input type="number" id="grupo" v-model.number="alumno.grupo" placeholder="Ej. 1" />
          </div>

          <!-- Campo: Correo -->
          <div class="form-group">
            <label for="correo">Correo electrónico</label>
            <input type="email" id="correo" v-model="alumno.correo" placeholder="correo@ejemplo.com" />
          </div>

          <!-- Campo: Estado -->
          <div class="form-group">
            <label for="estado">Estado</label>
            <select id="estado" v-model="alumno.status">
              <option value="">Seleccione</option>
              <option value="activo">Activo</option>
              <option value="inactivo">Inactivo</option>
            </select>
          </div>

          <!-- Botones -->
          <div class="button-group">
            <button type="button" class="register-rojo" @click="$emit('cerrar')">Cancelar</button>
            <button type="submit" class="register-verde">Registrar</button>
          </div>
        </form>

        <!-- Error -->
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

const alumno = ref({
  nombre_completo: '',
  matricula: '',
  password: '',
  grupo: null,
  correo: '',
  status: 'activo'
})

const error = ref(null)

const registrarAlumno = async () => {
  error.value = null
  try {
    await axios.post('/api/alumnos', {
      ...alumno.value,
      rol: 'alumno',
      created_by: 'frontend',
      modified_by: 'frontend'
    })

    emit('guardado')
    emit('cerrar')
  } catch (err) {
    error.value = err.response?.data?.message || 'Error al registrar alumno.'
    console.error('Error al registrar alumno:', err)
  }
}
</script>

<style src="@/../css/RegistroManual.css"></style>
