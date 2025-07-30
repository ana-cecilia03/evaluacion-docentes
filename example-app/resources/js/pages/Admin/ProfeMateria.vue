<template>
  <Menu>
    <div class="contenido-principal">
      <div class="relacion-filtros">

        <!-- Profesores activos -->
        <div class="relacion-columna">
          <select v-model="form.profesor_nombre">
            <option value="">Profesor</option>
            <option v-for="p in profesores" :key="p.id_profesor" :value="p.id_profesor">
              {{ p.nombre_completo }}
            </option>
          </select>
        </div>

        <!-- Periodos activos -->
        <div class="relacion-columna">
          <select v-model="form.periodo_num" @change="cargarCarreras">
            <option value="">Periodo</option>
            <option v-for="p in periodos" :key="p.id_periodo" :value="p.id_periodo">
              {{ p.num_periodo }}
            </option>
          </select>
        </div>

        <!-- Carreras -->
        <div class="relacion-columna">
          <select v-model="form.carrera_nom" @change="cargarMaterias">
            <option value="">Carrera</option>
            <option v-for="c in carreras" :key="c.id_carrera" :value="c.id_carrera">
              {{ c.nombre_carrera }}
            </option>
          </select>
        </div>

        <!-- Materias filtradas por carrera -->
        <div class="relacion-columna">
          <select v-model="form.materia_nom">
            <option value="">Materia</option>
            <option v-for="m in materias" :key="m.id_mat_cuatri_car" :value="m.materia_nombre">
              {{ m.materia_nombre }}
            </option>
          </select>
        </div>

        <!-- Grupos -->
        <div class="relacion-columna">
          <select v-model="form.clave">
            <option value="">Grupo</option>
            <option v-for="g in grupos" :key="g.id_grupo" :value="g.nombre">
              {{ g.nombre }}
            </option>
          </select>
        </div>

        <!-- Botón de enviar -->
        <div class="relacion-columna">
          <button class="btn-agregar" @click="agregarRelacion">Agregar</button>
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
              <td>{{ r.profesor?.nombre_completo ?? r.profesor_nombre }}</td>
              <td>{{ r.periodo?.num_periodo ?? r.periodo_num }}</td>
              <td>{{ r.carrera?.nombre_carrera ?? r.carrera_nom }}</td>
              <td>{{ r.materia_nom }}</td>
              <td>{{ r.grupo?.nombre ?? r.clave }}</td>
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

const profesores = ref([])
const periodos = ref([])
const carreras = ref([])
const materias = ref([])
const grupos = ref([])
const relaciones = ref([])

const form = ref({
  profesor_nombre: '',
  periodo_num: '',
  carrera_nom: '',
  materia_nom: '',
  clave: ''
})

onMounted(() => {
  axios.get('/api/profesores/activos').then(res => profesores.value = res.data)
  axios.get('/api/periodos/activos').then(res => periodos.value = res.data)
  axios.get('/api/grupos').then(res => grupos.value = res.data)
  cargarRelaciones()
})

const cargarCarreras = () => {
  axios.get('/api/carreras').then(res => carreras.value = res.data)
}

const cargarMaterias = () => {
  if (!form.value.carrera_nom) return
  axios.get(`/api/materias/por-carrera/${encodeURIComponent(form.value.carrera_nom)}`)
    .then(res => materias.value = res.data)
}

const cargarRelaciones = () => {
  axios.get('/api/relaciones').then(res => relaciones.value = res.data)
}

const agregarRelacion = async () => {
  const f = { ...form.value }
  if (!f.profesor_nombre || !f.periodo_num || !f.carrera_nom || !f.materia_nom || !f.clave) {
    alert('Completa todos los campos')
    return
  }

  try {
    await axios.post('/api/relaciones', f)
    cargarRelaciones()
    form.value = {
      profesor_nombre: '',
      periodo_num: '',
      carrera_nom: '',
      materia_nom: '',
      clave: ''
    }
  } catch (e) {
    console.error('Error al guardar relación:', e)
    alert('Error al guardar relación')
  }
}
</script>

<style src="@/../css/Registros.css"></style>
<style src="@/../css/relacion.css"></style>
<style src="@/../css/botones.css"></style>
