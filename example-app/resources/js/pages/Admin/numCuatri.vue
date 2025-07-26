<template>
  <Menu>
    <main class="contenido-principal">
      <!-- Filtros y botón Agregar -->
      <div class="relacion-filtros">
        <div class="relacion-columna">
          <label>Número:</label>
          <input type="number" v-model="nuevoCuatrimestre.num" placeholder="1-10" />
        </div>

        <div class="relacion-columna">
          <label>Nombre:</label>
          <input type="text" v-model="nuevoCuatrimestre.nombre" placeholder="Primer Cuatrimestre" />
        </div>

        <div class="relacion-columna">
          <label> </label>
          <button class="btn-agregar" @click="agregarCuatrimestre">Agregar</button>
        </div>
      </div>

      <!-- Tabla -->
      <div class="table-wrapper">
        <table>
          <thead>
            <tr>
              <th>No°</th>
              <th>Cuatrimestre</th>
              <th>Acción</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="cuatri in cuatrimestres" :key="cuatri.id_cuatrimestre">
              <td>{{ cuatri.num }}</td>
              <td>{{ cuatri.nombre }}</td>
              <td>
                <button class="boton-verde" @click="editarCuatrimestre(cuatri)">Editar</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </main>
  </Menu>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import Menu from '@/layouts/Menu.vue'

const cuatrimestres = ref([])
const nuevoCuatrimestre = ref({
  num: '',
  nombre: '',
  created_by: 'admin' // ajusta esto con el usuario actual si aplica
})

// Obtener cuatrimestres
const obtenerCuatrimestres = async () => {
  const response = await axios.get('/api/cuatrimestres')
  cuatrimestres.value = response.data
}

// Agregar cuatrimestre
const agregarCuatrimestre = async () => {
  try {
    await axios.post('/api/cuatrimestres', nuevoCuatrimestre.value)
    await obtenerCuatrimestres()
    nuevoCuatrimestre.value = { num: '', nombre: '', created_by: 'admin' }
  } catch (error) {
    alert('Error al agregar: ' + error.response.data.message)
  }
}

// Editar cuatrimestre (aquí puedes abrir un modal en lugar de inline)
const editarCuatrimestre = async (cuatri) => {
  const nuevoNombre = prompt('Nuevo nombre:', cuatri.nombre)
  const nuevoNum = prompt('Nuevo número:', cuatri.num)
  if (nuevoNombre && nuevoNum) {
    try {
      await axios.put(`/api/cuatrimestres/${cuatri.id_cuatrimestre}`, {
        nombre: nuevoNombre,
        num: nuevoNum,
        modified_by: 'admin'
      })
      await obtenerCuatrimestres()
    } catch (error) {
      alert('Error al editar: ' + error.response.data.message)
    }
  }
}

onMounted(obtenerCuatrimestres)
</script>

<style src="@/../css/Registros.css"></style>
<style src="@/../css/relacion.css"></style>
<style src="@/../css/botones.css"></style>
