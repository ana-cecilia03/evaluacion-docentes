<template>
  <Menu>
    <main class="contenido-principal">
      <!-- Barra de acciones -->
      <div class="actions">
        <input type="text" placeholder="Buscar alumno..." v-model="busqueda" />
        <select>
          <option value="">Filtrar por grupo</option>
          <option>Grupo A</option>
          <option>Grupo B</option>
        </select>
        <button class="btn-register" @click="mostrarFormulario = true">Registrar Alumno Manual</button>
        <button class="btn-upload" @click="mostrarCSV = true">Cargar CSV</button>

        <!-- Botón para desactivar múltiples alumnos -->
        <button
          class="btn-grupal"
          :disabled="alumnosSeleccionados.length === 0"
          @click="realizarAccionGrupal"
        >
          DESACTIVAR ({{ alumnosSeleccionados.length }})
        </button>
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
              <th>
                <input
                  type="checkbox"
                  @change="toggleSeleccionarTodos"
                  :checked="todosSeleccionados"
                />
              </th>
              <th>Matrícula</th>
              <th>Nombre</th>
              <th>Curp</th>
              <th>Grupo</th>
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
              <td>{{ alumno.curp }}</td>
              <td>{{ alumno.grupo }}</td>
              <td>{{ alumno.status }}</td>
              <td>
                <button class="btn-edit" @click="abrirEdicion(alumno)">Editar</button>
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

// Estados reactivos
const mostrarFormulario = ref(false)
const mostrarCSV = ref(false)
const editarDatos = ref(false)

const alumnos = ref([])
const alumnoSeleccionado = ref(null)
const alumnosSeleccionados = ref([])

const busqueda = ref('')

// Cargar alumnos desde el backend
const cargarAlumnos = async () => {
  try {
    const response = await axios.get('/api/alumnos')
    alumnos.value = response.data
  } catch (error) {
    console.error('Error al cargar alumnos:', error)
  }
}

// Filtrar alumnos por búsqueda
const alumnosFiltrados = computed(() => {
  if (!busqueda.value.trim()) return alumnos.value
  return alumnos.value.filter(a =>
    a.nombre_completo.toLowerCase().includes(busqueda.value.toLowerCase()) ||
    a.matricula.toLowerCase().includes(busqueda.value.toLowerCase()) ||
    a.curp.toLowerCase().includes(busqueda.value.toLowerCase())
  )
})

// Verificar si todos los alumnos están seleccionados
const todosSeleccionados = computed(() =>
  alumnosFiltrados.value.length > 0 &&
  alumnosFiltrados.value.every(a => alumnosSeleccionados.value.includes(a.id_alumno))
)

// Alternar selección de todos
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

// Abrir formulario de edición
const abrirEdicion = (alumno) => {
  alumnoSeleccionado.value = alumno
  editarDatos.value = true
}

// Acción grupal: desactivar seleccionados
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

// Al cargar el componente
onMounted(() => {
  cargarAlumnos()
})
</script>

<style src="@/../css/Registros.css"></style>
