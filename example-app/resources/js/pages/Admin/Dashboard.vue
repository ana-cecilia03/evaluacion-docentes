<template>
  <Menu>
    <div class="contenido-periodo">
      <div class="contenedor-cuadro">
        <h1 class="titulo">Crear periodo</h1>
        
        <form class="formulario-periodo" @submit.prevent="crearPeriodo">
          <div class="campo-formulario">
            <label for="numero">Número:</label>
            <input type="number" id="numero" v-model="form.num_periodo" required />
          </div>

          <div class="campo-formulario">
            <label for="nombre">Nombre:</label>
            <!-- CAMBIO: reemplazado <select> por <input type="text"> -->
            <input
              type="text"
              id="nombre"
              v-model="form.nombre"
              placeholder="Ej. Primer Cuatrimestre"
              required
            />
          </div>

          <div class="campo-formulario">
            <label for="inicio">Fecha de inicio:</label>
            <input type="date" id="inicio" v-model="form.inicio" required />
          </div>

          <div class="campo-formulario">
            <label for="fin">Fecha de término:</label>
            <input type="date" id="fin" v-model="form.fin" required />
          </div>

          <div class="botones-formulario">
            <button type="button" class="btn-cancelar" @click="cancelar">Cancelar</button>
            <button type="submit" class="btn-crear">Crear</button>
          </div>

          <p v-if="mensaje" class="mensaje-exito">{{ mensaje }}</p>
          <p v-if="error" class="mensaje-error">{{ error }}</p>
        </form>
      </div>
    </div>
  </Menu>
</template>

<script setup>
import Menu from '@/layouts/Menu.vue'
import { ref } from 'vue'
import axios from 'axios'

// Datos del formulario
const form = ref({
  num_periodo: '',
  nombre: '',
  inicio: '',
  fin: ''
})

const mensaje = ref('')
const error = ref('')

// Enviar al backend
const crearPeriodo = async () => {
  try {
    const response = await axios.post('/api/periodos', {
      num_periodo: form.value.num_periodo,
      nombre_periodo: form.value.nombre,
      fecha_inicio: form.value.inicio,
      fecha_fin: form.value.fin,
    })
    mensaje.value = response.data.message
    error.value = ''
    cancelar()
  } catch (err) {
    mensaje.value = ''
    error.value = err.response?.data?.message || 'Error al guardar el período.'
  }
}

// Cancelar formulario
function cancelar() {
  form.value = { num_periodo: '', nombre: '', inicio: '', fin: '' }
}
</script>

<style src="@/../css/DashboardAdmin.css" scoped></style>
