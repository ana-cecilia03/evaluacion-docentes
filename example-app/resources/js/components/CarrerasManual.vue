<template>
  <teleport to="body">
  <!-- Modal de registro manual de carrera -->
  <div class="modal-overlay">
    <div class="modal-content">
      <div class="register-container">
        <h1 class="titulo">Registrar Carrera</h1>

        <form @submit.prevent="registrarCarrera" class="register-form">
          <!-- Campo: CLAVE -->
          <div class="form-group">
            <label for="clave">Clave</label>
            <input type="text" id="clave" v-model="carrera.clave" placeholder="Ej. MSC4" />
          </div>

          <!-- Campo: nombre -->
          <div class="form-group">
            <label for="nombre">Nombre</label>
            <input type="text" id="nombre" v-model="carrera.nombre" placeholder="Ingeniería Robótica" />
          </div>

          <!-- Botones -->
          <div class="button-group">
            <button type="button" class="register-rojo" @click="$emit('cerrar')">Cancelar</button>
            <button type="submit" class="register-verde">Registrar</button>
          </div>

          <!-- Mensaje de error -->
          <p v-if="error" class="mensaje-error">{{ error }}</p>
        </form>
      </div>
    </div>
  </div>
  </teleport>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'

const emit = defineEmits(['cerrar', 'guardado'])

const carrera = ref({
  clave: '',
  nombre: ''
})

const error = ref('')

const registrarCarrera = async () => {
  try {
    await axios.post('/api/carreras', carrera.value)

    // ✅ Notificar al componente padre que se guardó correctamente
    emit('guardado')

    // ✅ Cerrar el modal
    emit('cerrar')
  } catch (err) {
    console.error('Error al registrar carrera:', err)
    error.value = err.response?.data?.message || 'Error al registrar la carrera'
  }
}
</script>

<style src="@/../css/RegistroManual.css"></style>
