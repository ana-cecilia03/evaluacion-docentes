<template>
  <Menu>
    <main class="contenido-principal">
      <!-- Filtros y botón Agregar -->
      <div class="relacion-filtros">
        <div class="relacion-columna">
          <label>No°:</label>
          <input type="number" v-model="nuevoCuatrimestre.num" placeholder="1-10" />
        </div>

        <div class="relacion-columna">
          <label>Cuatrimestre:</label>
          <input type="text" v-model="nuevoCuatrimestre.nombre" placeholder="Primer Cuatrimestre" />
        </div>

        <div class="relacion-columna">
          <label> </label>
          <button class="boton-azul" @click="agregarCuatrimestre">Agregar</button>
        </div>
      </div>

      <!-- Modal: Edición -->
      <EditarCuatri
        v-if="modoEditar"
        :relacion="datosEdicion"
        @cerrar="modoEditar = false"
        @guardar="actualizarCuatrimestre"
      />

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
import EditarCuatri from '@/components/EditarCuatri.vue'

const modoEditar = ref(false)
const datosEdicion = ref({})

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
const editarCuatrimestre = (cuatri) => {
  datosEdicion.value = { ...cuatri }
  modoEditar.value = true
}

const actualizarCuatrimestre = async (cuatriActualizado) => {
  try {
    await axios.put(`/api/cuatrimestres/${cuatriActualizado.id_cuatrimestre}`, cuatriActualizado)
    modoEditar.value = false
    await obtenerCuatrimestres()
  } catch (error) {
    alert('Error al actualizar: ' + error.response.data.message)
  }
}


onMounted(obtenerCuatrimestres)
</script>

