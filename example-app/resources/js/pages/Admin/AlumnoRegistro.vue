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
              <th>Matrícula</th>
              <th>Nombre</th>
              <th>Curp</th>
              <th>Grupo</th>
              <th>Estado</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="alumno in alumnosFiltrados" :key="alumno.id_alumno">
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

// Modales y datos
const mostrarFormulario = ref(false)
const mostrarCSV = ref(false)
const editarDatos = ref(false)

const alumnos = ref([])
const alumnoSeleccionado = ref(null)
const busqueda = ref('')

// Función para cargar desde backend
const cargarAlumnos = async () => {
  try {
    const response = await axios.get('/api/alumnos')
    alumnos.value = response.data
  } catch (error) {
    console.error('Error al cargar alumnos:', error)
  }
}

// Buscar alumnos
const alumnosFiltrados = computed(() => {
  if (!busqueda.value.trim()) return alumnos.value
  return alumnos.value.filter(a =>
    a.nombre_completo.toLowerCase().includes(busqueda.value.toLowerCase()) ||
    a.matricula.toLowerCase().includes(busqueda.value.toLowerCase()) ||
    a.curp.toLowerCase().includes(busqueda.value.toLowerCase())
  )
})

// Abrir edición
const abrirEdicion = (alumno) => {
  alumnoSeleccionado.value = alumno
  editarDatos.value = true
}

// Inicializar datos al montar
onMounted(() => {
  cargarAlumnos()
})
</script>

<style src="@/../css/Registros.css"></style>
