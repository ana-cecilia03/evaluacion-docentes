<template>
  <Menu>
    <main class="contenido-principal">
      <!-- Barra de acciones -->
      <div class="actions">
        <input type="text" placeholder="Buscar alumno..." v-model="busqueda" />

        <select v-model="grupoSeleccionado">
          <option value="">Filtrar por grupo</option>
          <option v-for="grupo in grupos" :key="grupo.id_grupo" :value="grupo.id_grupo">
            {{ grupo.nombre }}
          </option>
        </select>

        <button class="btn-register" @click="mostrarFormulario = true">Registrar Alumno Manual</button>
        <button class="btn-upload" @click="mostrarCSV = true">Cargar CSV</button>

        <template v-if="alumnosSeleccionados.length > 0">
          <button class="btn-grupal" @click="realizarAccionGrupal">
            DESACTIVAR ({{ alumnosSeleccionados.length }})
          </button>
        </template>
      </div>

      <!-- Modal: Registro Manual -->
      <div v-if="mostrarFormulario" class="modal-overlay">
        <div class="modal-content">
          <AlumnosManual @cerrar="mostrarFormulario = false" @guardado="cargarAlumnos" />
        </div>
      </div>

      <!-- Modal: Carga CSV -->
      <div v-if="mostrarCSV" class="modal-csv">
        <div class="modal-content">
          <CsvAlumnos @cerrar="mostrarCSV = false" @guardado="cargarAlumnos" />
        </div>
      </div>

      <!-- Modal: Edición -->
      <div v-if="editarDatos" class="modal-overlay">
        <div class="modal-content">
          <EditarAlumno
            :alumno="alumnoSeleccionado"
            @cerrar="editarDatos = false"
            @actualizado="cargarAlumnos"
          />
        </div>
      </div>

      <!-- Tabla dinámica de alumnos -->
      <div class="table-wrapper">
        <table>
          <thead>
            <tr>
              <th><input type="checkbox" @change="toggleSeleccionarTodos" :checked="todosSeleccionados" /></th>
              <th>Matrícula</th>
              <th>Nombre</th>
              <th>Grupo</th>
              <th>Correo</th>
              <th>Estado</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="alumno in alumnosFiltrados"
              :key="alumno.id_alumno"
              :class="{ 'fila-inactiva': alumno.status === 'inactivo' }"
            >
              <td>
                <input
                  type="checkbox"
                  :value="alumno.id_alumno"
                  v-model="alumnosSeleccionados"
                />
              </td>
              <td>{{ alumno.matricula }}</td>
              <td>{{ alumno.nombre_completo }}</td>
              <td>{{ alumno.grupo?.nombre || 'Sin grupo' }}</td>
              <td>{{ alumno.correo }}</td>
              <td>{{ alumno.status }}</td>
              <td>
                <button class="boton-verde" @click="abrirEdicion(alumno)">Editar</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </main>
  </Menu>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'
import Menu from '@/layouts/Menu.vue'
import AlumnosManual from '@/components/AlumnosManual.vue'
import CsvAlumnos from '@/components/CsvAlumnos.vue'
import EditarAlumno from '@/components/EditarAlumno.vue'

// Estados
const mostrarFormulario = ref(false)
const mostrarCSV = ref(false)
const editarDatos = ref(false)
const alumnos = ref([])
const alumnoSeleccionado = ref(null)
const alumnosSeleccionados = ref([])

// Filtros
const busqueda = ref('')
const grupos = ref([])
const grupoSeleccionado = ref('')

// Funciones
const cargarAlumnos = async () => {
  try {
    const response = await axios.get('/api/alumnos')
    alumnos.value = response.data
  } catch (error) {
    console.error('Error al cargar alumnos:', error)
  }
}

const cargarGrupos = async () => {
  try {
    const response = await axios.get('/api/grupos')
    grupos.value = response.data
  } catch (error) {
    console.error('Error al cargar grupos:', error)
  }
}

const alumnosFiltrados = computed(() => {
  return alumnos.value.filter(a => {
    const coincideBusqueda =
      a.nombre_completo.toLowerCase().includes(busqueda.value.toLowerCase()) ||
      a.matricula.toLowerCase().includes(busqueda.value.toLowerCase())
    const coincideGrupo = !grupoSeleccionado.value || a.grupo?.id_grupo == grupoSeleccionado.value
    return coincideBusqueda && coincideGrupo
  })
})

const todosSeleccionados = computed(() =>
  alumnosFiltrados.value.length > 0 &&
  alumnosFiltrados.value.every(a => alumnosSeleccionados.value.includes(a.id_alumno))
)

const toggleSeleccionarTodos = () => {
  if (todosSeleccionados.value) {
    alumnosSeleccionados.value = alumnosSeleccionados.value.filter(
      id => !alumnosFiltrados.value.some(a => a.id_alumno === id)
    )
  } else {
    const nuevos = alumnosFiltrados.value.map(a => a.id_alumno)
    alumnosSeleccionados.value = Array.from(new Set([...alumnosSeleccionados.value, ...nuevos]))
  }
}

const abrirEdicion = (alumno) => {
  alumnoSeleccionado.value = alumno
  editarDatos.value = true
}

const realizarAccionGrupal = async () => {
  if (alumnosSeleccionados.value.length === 0) return

  try {
    const response = await axios.post('/api/alumnos/desactivar', {
      ids: alumnosSeleccionados.value
    })
    alert(response.data.message || 'Alumnos desactivados correctamente.')
    alumnosSeleccionados.value = []
    await cargarAlumnos()
  } catch (error) {
    console.error(error)
    alert('Error al desactivar los alumnos.')
  }
}

// Inicializar
onMounted(() => {
  cargarAlumnos()
  cargarGrupos()
})
</script>

<style src="@/../css/Registros.css"></style>
<style src="@/../css/botones.css"></style>
