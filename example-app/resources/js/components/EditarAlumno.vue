<template>
  <!-- Modal de edición de alumno -->
  <div class="modal-overlay">
    <div class="modal-content">
      <div class="register-container">
        <h1 class="titulo">Actualizar Alumno</h1>

        <form @submit.prevent="actualizarAlumno" class="register-form">
          <!-- Campo: Nombre completo -->
          <div class="form-group">
            <label for="nombre">Nombre completo</label>
            <input
              type="text"
              id="nombre"
              v-model="alumnoLocal.nombre_completo"
              placeholder="Ej. Ana López"
            />
          </div>

          <!-- Campo: Matrícula -->
          <div class="form-group">
            <label for="matricula">Matrícula</label>
            <input
              type="text"
              id="matricula"
              v-model="alumnoLocal.matricula"
              placeholder="Ej. 20230101"
            />
          </div>

          <!-- Campo: Correo -->
          <div class="form-group">
            <label for="correo">Correo electrónico</label>
            <input
              type="email"
              id="correo"
              v-model="alumnoLocal.correo"
              placeholder="correo@ejemplo.com"
            />
          </div>

          <!-- Campo: Contraseña -->
          <div class="form-group">
            <label for="password">Contraseña</label>
            <input
              type="password"
              id="password"
              v-model="alumnoLocal.password"
              placeholder="Nueva contraseña o deje vacío para no cambiarla"
            />
          </div>

          <!-- Campo: Grupo -->
          <div class="form-group">
            <label for="grupo">Grupo</label>
            <select id="grupo" v-model="alumnoLocal.grupo">
              <option value="">Seleccione un grupo</option>
              <option v-for="grupo in grupos" :key="grupo.id_grupo" :value="grupo.id_grupo">
                {{ grupo.nombre }}
              </option>
            </select>
          </div>

          <!-- Campo: Estado -->
          <div class="form-group">
            <label for="estado">Estado</label>
            <select id="estado" v-model="alumnoLocal.status">
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

        <!-- Error -->
        <div v-if="error" class="error-message">
          {{ error }}
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue'
import axios from 'axios'
import { defineProps, defineEmits } from 'vue'

const emit = defineEmits(['cerrar', 'actualizado'])

const props = defineProps({
  alumno: {
    type: Object,
    required: true
  }
})

// Estado local para edición
const alumnoLocal = ref({
  id_alumno: null,
  nombre_completo: '',
  matricula: '',
  correo: '',
  password: '',
  grupo: '',
  status: ''
})

const grupos = ref([])
const error = ref(null)

// Cargar lista de grupos desde API
const cargarGrupos = async () => {
  try {
    const res = await axios.get('/api/grupos')
    grupos.value = res.data
  } catch (e) {
    console.error('Error al cargar grupos:', e)
  }
}

// Copiar datos de la prop al estado local
watch(
  () => props.alumno,
  (nuevo) => {
    if (nuevo) {
      alumnoLocal.value = {
        ...nuevo,
        password: ''
      }
    }
  },
  { immediate: true }
)

const actualizarAlumno = async () => {
  error.value = null

  try {
    const payload = {
      nombre_completo: alumnoLocal.value.nombre_completo,
      matricula: alumnoLocal.value.matricula,
      correo: alumnoLocal.value.correo,
      grupo: alumnoLocal.value.grupo,
      status: alumnoLocal.value.status,
      modified_by: 'frontend'
    }

    if (alumnoLocal.value.password?.trim()) {
      payload.password = alumnoLocal.value.password
    }

    await axios.put(`/api/alumnos/${alumnoLocal.value.id_alumno}`, payload)

    emit('actualizado')
    emit('cerrar')
  } catch (err) {
    error.value = err.response?.data?.message || 'Error al actualizar alumno.'
    console.error('Error al actualizar alumno:', err)
  }
}

onMounted(() => {
  cargarGrupos()
})
</script>

<style src="@css/global.css" lang="css"></style>
