<template>
  <Menu>
    <div class="contenido-principal">
      <!-- Filtros para seleccionar cada elemento de la relación -->
      <div class="relacion-filtros">

        <!-- Select: Profesores -->
        <!-- Carga desde API: /api/profesores -->
        <div class="relacion-columna">
          <select v-model="form.profesor_nombre">
            <option value="">Profesor</option>
            <option v-for="p in profesores" :key="p.id_profesor" :value="p.id_profesor">
              {{ p.nombre_completo }}
            </option>
          </select>
        </div>

        <!-- Select: Periodos -->
        <!-- Carga desde API: /api/periodos -->
        <!-- Al cambiar, se llama a cargarRelacionados -->
        <div class="relacion-columna">
          <select v-model="form.periodo_num" @change="cargarRelacionados">
            <option value="">Periodo</option>
            <option v-for="p in periodos" :key="p.id_periodo" :value="p.num_periodo">
              {{ p.num_periodo }}
            </option>
          </select>
        </div>

        <!-- Select: Carreras -->
        <!-- Carga desde API: /api/carreras -->
        <!-- Devuelve datos desde tabla mat_cuatri_carr -->
        <div class="relacion-columna">
          <select v-model="form.id_mat_cuatri_car">
            <option value="">Carreras</option>
            <option
              v-for="c in carreras"
              :key="c.id_mat_cuatri_car"
              :value="c.id_mat_cuatri_car"
            >
              {{ c.carrera_nombre }}
            </option>
          </select>
        </div>

        <!-- Select: Materias -->
        <!-- Carga desde API: /api/materias -->
        <!-- Aquí estamos esperando campo: nombre_materia -->
        <div class="relacion-columna">
          <select v-model="form.materia_nom">
            <option value="">Materia</option>
            <option
              v-for="m in materias"
              :key="m.id_materia"
              :value="m.nombre_materia"
            >
              {{ m.nombre_materia }}
            </option>
          </select>
        </div>

        <!-- Select: Grupos -->
        <!-- Carga desde API: /api/grupos -->
        <div class="relacion-columna">
          <select v-model="form.clave">
            <option value="">Grupo</option>
            <option v-for="g in grupos" :key="g.clave" :value="g.clave">
              {{ g.clave }}
            </option>
          </select>
        </div>

        <!-- Botón para enviar la relación -->
        <div class="relacion-columna">
          <button class="boton-azul" @click="agregarRelacion">Agregar</button>
        </div>
      </div>

      <!-- Modal: Edición -->
       <EditarProf v-if="modoEditar" 
       :relacion="datosEdicion" 
       @cerrar="modoEditar = false; obtenerDatos()" />      

      <!-- Tabla: muestra las relaciones ya registradas -->
      <!-- Carga desde API: /api/relaciones -->
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
              <td>{{ r.profesor_nombre }}</td>
              <td>{{ r.periodo_num }}</td>
              <td>{{ r.carrera_nom }}</td>
              <td>{{ r.materia_nom }}</td>
              <td>{{ r.clave }}</td>
              <td><button class="boton-verde" >Editar</button></td>
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

// Datos que llenan los selects
const profesores = ref([])
const periodos = ref([])
const carreras = ref([])
const materias = ref([])
const grupos = ref([])

// Datos de la tabla
const relaciones = ref([])

// Formulario a enviar
const form = ref({
  profesor_nombre: '',
  periodo_num: '',
  id_mat_cuatri_car: '',
  materia_nom: '',
  clave: ''
})

// Al cargar la vista
onMounted(() => {
  axios.get('/api/profesores').then(res => {
    console.log('Profesores:', res.data)
    profesores.value = res.data
  })

  axios.get('/api/periodos').then(res => {
    console.log('Periodos:', res.data)
    periodos.value = res.data
  })

  axios.get('/api/grupos').then(res => {
    console.log('Grupos:', res.data)
    grupos.value = res.data
  })

  cargarRelaciones()
})

// Cuando se cambia periodo, se cargan carreras y materias relacionadas
const cargarRelacionados = () => {
  axios.get('/api/carreras').then(res => {
    console.log('Carreras:', res.data)
    carreras.value = res.data
  })

  axios.get('/api/materias').then(res => {
    console.log('Materias:', res.data)
    materias.value = res.data
  })
}

// Cargar tabla de relaciones actuales
const cargarRelaciones = () => {
  axios.get('/api/relaciones').then(res => {
    console.log('Relaciones:', res.data)
    relaciones.value = res.data
  })
}

// Enviar nueva relación al backend
const agregarRelacion = async () => {
  const f = { ...form.value }

  if (!f.profesor_nombre || !f.periodo_num || !f.id_mat_cuatri_car || !f.materia_nom || !f.clave) {
    alert('Completa todos los campos')
    return
  }

  try {
    console.log('Enviando relación:', f)
    await axios.post('/api/relaciones', f)
    cargarRelaciones()
    form.value = {
      profesor_nombre: '',
      periodo_num: '',
      id_mat_cuatri_car: '',
      materia_nom: '',
      clave: ''
    }
  } catch (e) {
    console.error('Error al guardar relación:', e)
    alert('Error al guardar relación')
  }
}
</script>

<!-- Estilos -->
<style src="@/../css/Registros.css"></style>
<style src="@/../css/relacion.css"></style>
<style src="@/../css/botones.css"></style>
