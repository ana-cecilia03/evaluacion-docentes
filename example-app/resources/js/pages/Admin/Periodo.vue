<template>
  <Menu>
    <div class="contenido-periodo">
      <h1 class="titulo">Historial de periodos</h1>
      <div class="table-wrapper">
        <table class="tabla-periodos">
          <thead>
            <tr>
              <th>#</th>
              <th>Nombre</th>
              <th>Fecha de inicio</th>
              <th>Fecha de término</th>
              <th></th>
              <th>Acciones</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <!-- 🔁 Reemplaza el contenido estático con un v-for -->
            <tr v-for="(periodo, index) in periodos" :key="periodo.id_periodo">
              <td>{{ index + 1 }}</td>
              <td>{{ periodo.nombre_periodo }}</td>
              <td>{{ formatFecha(periodo.fecha_inicio) }}</td>
              <td>{{ formatFecha(periodo.fecha_fin) }}</td>
              <td><button class="boton-verde">Activar</button></td>
              <td><button class="boton-rojo">Desactivar</button></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </Menu>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import Menu from '@/layouts/Menu.vue'

// 🔄 Lista de periodos desde la API
const periodos = ref([])

const cargarPeriodos = async () => {
  try {
    const response = await axios.get('/api/periodos')
    periodos.value = response.data
  } catch (error) {
    console.error('Error al cargar los periodos', error)
  }
}

// 📅 Formatear fecha a dd/mm/aaaa
const formatFecha = (fecha) => {
  if (!fecha) return ''
  const partes = fecha.split('T')[0].split('-') // yyyy-mm-dd
  return `${partes[2]}/${partes[1]}/${partes[0]}`
}


// 🚀 Cargar los datos al montar el componente
onMounted(() => {
  cargarPeriodos()
})
</script>

<style src="@/../css/Contenido.css"></style>
<style src="@/../css/botones.css"></style>
