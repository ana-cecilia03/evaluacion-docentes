<template>
  <Menu>
    <div class="contenido-periodo">
      <h1 class="titulo">Historial de periodos</h1>

      <form @submit.prevent="guardarPeriodo" class="formulario-periodo">
        <input type="text" v-model="nombre_periodo" placeholder="Nombre del periodo" />
        <input type="date" v-model="fecha_inicio" placeholder="Fecha de inicio" />
        <input type="date" v-model="fecha_fin" placeholder="Fecha de término" />
        <button type="submit" class="boton-verde">Guardar periodo</button>
        <p v-if="mensaje" class="mensaje-exito">{{ mensaje }}</p>
        <p v-if="error" class="mensaje-error">{{ error }}</p>
      </form>
    </div>
  </Menu>
</template>

<script setup>
import Menu from '@/layouts/Menu.vue'
import { ref } from 'vue'
import axios from 'axios'

const nombre_periodo = ref('')
const fecha_inicio = ref('')
const fecha_fin = ref('')
const mensaje = ref('')
const error = ref('')

const guardarPeriodo = async () => {
  try {
    const response = await axios.post('/api/periodos', {
      nombre_periodo: nombre_periodo.value,
      fecha_inicio: fecha_inicio.value,
      fecha_fin: fecha_fin.value,
    })

    mensaje.value = response.data.message
    error.value = ''
    nombre_periodo.value = ''
    fecha_inicio.value = ''
    fecha_fin.value = ''
  } catch (err) {
    mensaje.value = ''
    error.value = err.response?.data?.message || 'Error al guardar.'
  }
}
</script>

<style src="@/../css/Contenido.css"></style>
<style src="@/../css/botones.css"></style>
