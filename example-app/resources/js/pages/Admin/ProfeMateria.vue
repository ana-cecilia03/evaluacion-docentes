<template>
  <Menu>
    <div class="contenido-principal">
      <div class="relacion-filtros">

        <!-- Profesores activos -->
        <div class="relacion-columna">
          <select v-model="form.profesor_id">
            <option value="">Profesor</option>
            <option v-for="p in profesores" :key="p.id_profesor" :value="p.id_profesor">
              {{ p.nombre_completo }}
            </option>
          </select>
        </div>

        <!-- Periodos activos -->
        <div class="relacion-columna">
          <select v-model="form.periodo_id" @change="cargarCarreras">
            <option value="">Periodo</option>
            <option v-for="p in periodos" :key="p.id_periodo" :value="p.id_periodo">
              {{ p.nombre_periodo }}
            </option>
          </select>
        </div>

        <!-- Carreras -->
        <div class="relacion-columna">
          <select v-model="form.carrera_nombre" @change="cargarMaterias">
            <option value="">Carrera</option>
            <option v-for="c in carreras" :key="c.id_carrera" :value="c.nombre_carrera">
              {{ c.nombre_carrera }}
            </option>
          </select>
        </div>

        <!-- Materias -->
        <div class="relacion-columna">
          <select v-model="form.id_mat_cuatri_car">
            <option value="">Materia</option>
            <option v-for="m in materias" :key="m.id_mat_cuatri_car" :value="m.id_mat_cuatri_car">
              {{ m.materia_nombre }} (Cuatri {{ m.cuatri_num }})
            </option>
          </select>
        </div>

        <!-- Grupos -->
        <div class="relacion-columna">
          <select v-model="form.grupo_id">
            <option value="">Grupo</option>
            <option v-for="g in grupos" :key="g.id_grupo" :value="g.id_grupo">
              {{ g.nombre }}
            </option>
          </select>
        </div>

        <div class="relacion-columna">
          <button class="boton-azul" @click="agregarRelacion">Agregar</button>
        </div>
      </div>

      <!-- Tabla relaciones -->

      <div class="table-wrapper">
        <table>
          <thead>
            <tr>
              <th>Profesor</th>
              <th>Periodo</th>
              <th>Carrera</th>
              <th>Materia</th>
              <th>Grupo</th>
              <th>Acción</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="r in relaciones" :key="r.id_relacion">

              <td>{{ r.profesor?.nombre_completo }}</td>
              <td>{{ r.periodo?.nombre_periodo }}</td>
              <td>{{ r.mat_cuatri_car?.carrera_nombre }}</td>
              <td>{{ r.mat_cuatri_car?.materia_nombre }}</td>
              <td>{{ r.grupo?.nombre }}</td>
              <td><button class="boton-verde">Editar</button></td>
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
import EditarProf from '@/components/EditarProf.vue'

const profesores = ref([])
const periodos = ref([])
const carreras = ref([])
const materias = ref([])
const grupos = ref([])
const relaciones = ref([])

const form = ref({
  profesor_id: '',
  periodo_id: '',
  carrera_nombre: '',
  id_mat_cuatri_car: '',
  grupo_id: ''
})

onMounted(() => {
  axios.get('/api/profesores/activos').then(res => profesores.value = res.data)
  axios.get('/api/periodos/activos').then(res => periodos.value = res.data)
  axios.get('/api/carreras').then(res => carreras.value = res.data)
  axios.get('/api/grupos').then(res => grupos.value = res.data)
  cargarRelaciones()
})

const cargarCarreras = () => {
  axios.get('/api/carreras').then(res => carreras.value = res.data)
}

const cargarMaterias = () => {
  if (!form.value.carrera_nombre) return
  axios.get(`/api/materias/por-carrera/${encodeURIComponent(form.value.carrera_nombre)}`)
    .then(res => materias.value = res.data)
}

const cargarRelaciones = () => {
  axios.get('/api/relaciones').then(res => relaciones.value = res.data)
}

const agregarRelacion = async () => {
  const f = { ...form.value, modified_by: 'admin' }
  if (!f.profesor_id || !f.periodo_id || !f.id_mat_cuatri_car || !f.grupo_id) {
    alert('Completa todos los campos')
    return
  }
  try {
    await axios.post('/api/relaciones', f)
    alert('Relación registrada')
    form.value = {
      profesor_id: '', periodo_id: '', carrera_nombre: '', id_mat_cuatri_car: '', grupo_id: ''
    }
    cargarRelaciones()
  } catch (e) {
    console.error(e)
    alert('Error al guardar relación')
  }
}
</script>

<style src="@/../css/Registros.css"></style>
<style src="@/../css/relacion.css"></style>
<style src="@/../css/botones.css"></style>