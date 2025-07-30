<template>
  <Menu>
    <main class="contenido-principal">
      <!-- Filtros y botón Agregar -->
      <div class="relacion-filtros">
        <div class="relacion-columna">
          <select v-model="form.carrera_nombre">
            <option value="">Carreras</option>
            <option v-for="c in carreras" :key="c.nombre_carrera" :value="c.nombre_carrera">
              {{ c.nombre_carrera }}
            </option>
          </select>
        </div>

        <div class="relacion-columna">
          <select v-model="form.cuatri_num">
            <option value="">Cuatrimestre</option>
            <option v-for="c in cuatrimestres" :key="c.num" :value="c.num">
              {{ c.num }}
            </option>
          </select>
        </div>

        <div class="relacion-columna">
          <select v-model="form.materia_nombre">
            <option value="">Materia</option>
            <option v-for="m in materias" :key="m.nombre_materia" :value="m.nombre_materia">
              {{ m.nombre_materia }}
            </option>
          </select>
        </div>

        <div class="relacion-columna">
          <button class="boton-azul" @click="agregarRelacion">Agregar</button>
        </div>
      </div>

      <!-- Modal: Edición -->
       <EditarMat v-if="modoEditar" 
       :relacion="datosEdicion" 
       @cerrar="modoEditar = false; obtenerDatos()" />

      <!-- Tabla -->
      <div class="table-wrapper">
        <table>
          <thead>
            <tr>
              <th>Carrera</th>
              <th>Cuatrimestre</th>
              <th>Materia</th>
              <th>Acción</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="rel in relaciones" :key="rel.id_mat_cuatri_car">
              <td>{{ rel.carrera_nombre }}</td>
              <td>{{ rel.cuatri_num }}</td>
              <td>{{ rel.materia_nombre }}</td>
              <td>
                <button class="boton-verde" @click="editarRelacion(rel)">Editar</button>
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
import EditarMat from '@/components/EditarMat.vue'

const modoEditar = ref(false)
const datosEdicion = ref(null)

const carreras = ref([])
const cuatrimestres = ref([])
const materias = ref([])
const relaciones = ref([])

const form = ref({
  carrera_nombre: '',
  cuatri_num: '',
  materia_nombre: ''
})

const editarRelacion = (rel) => {
  datosEdicion.value = { ...rel }
  modoEditar.value = true
}

const obtenerDatos = async () => {
  const [resCarreras, resCuatris, resMaterias, resRelaciones] = await Promise.all([
    axios.get('/api/carreras/nombres'),
    axios.get('/api/cuatrimestres/numeros'),
    axios.get('/api/materias/nombres'),
    axios.get('/api/mat-cuatri-car')
  ])
  carreras.value = resCarreras.data
  cuatrimestres.value = resCuatris.data
  materias.value = resMaterias.data
  relaciones.value = resRelaciones.data
}

const agregarRelacion = async () => {
  if (!form.value.carrera_nombre || !form.value.cuatri_num || !form.value.materia_nombre) {
    alert('Completa todos los campos')
    return
  }

  try {
    await axios.post('/api/mat-cuatri-car', form.value)
    await obtenerDatos()
    form.value = { carrera_nombre: '', cuatri_num: '', materia_nombre: '' }
  } catch (error) {
    alert('Error al guardar: ' + error.response.data.message)
  }
}

onMounted(obtenerDatos)
</script>

<style src="@/../css/Registros.css"></style>
<style src="@/../css/relacion.css"></style>
<style src="@/../css/botones.css"></style>
