<template>
  <Menu>
    <main class="contenido-principal">
      <!-- Barra de acciones -->
      <div class="actions">
        <input type="text" placeholder="Buscar por nombre o matrícula" v-model="busqueda" />
        <select v-model="filtroEstado">
          <option value="">Estado</option>
          <option value="activo">Activo</option>
          <option value="inactivo">Inactivo</option>
        </select>
        <button class="btn-register" @click="mostrarFormulario = true">Registrar Profesor Manual</button>
        <button class="btn-upload" @click="mostrarCSV = true">Cargar CSV</button>
      </div>

      <!-- Modal: Registro Manual -->
      <div v-if="mostrarFormulario" class="modal-overlay">
        <div class="modal-content">
          <ProfesoresManual @cerrar="mostrarFormulario = false" @guardado="obtenerProfesores" />
        </div>
      </div>

      <!-- Modal: Carga CSV -->
      <div v-if="mostrarCSV" class="modal-csv">
        <div class="modal-content">
          <CsvProfesores @cerrar="mostrarCSV = false" @guardado="obtenerProfesores" />
        </div>
      </div>

      <!-- Modal: Edición -->
      <div v-if="editarProfesores" class="modal-csv">
        <div class="modal-content">
          <EditarProfesores
            :profesor="profesorSeleccionado"
            @cerrar="editarProfesores = false"
            @actualizado="() => { editarProfesores = false; obtenerProfesores() }"
          />
        </div>
      </div>

      <!-- Tabla con profesores -->
      <div class="table-wrapper">
        <table>
          <thead>
            <tr>
              <th>Matrícula</th>
              <th>Nombre</th>
              <th>Correo</th>
              <th>Estado</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="profesor in profesoresFiltrados"
              :key="profesor.id_profesor"
              :class="{ 'fila-inactiva': profesor.status === 'inactivo' }"
            >
              <td>{{ profesor.matricula }}</td>
              <td>{{ profesor.nombre_completo }}</td>
              <td>{{ profesor.correo }}</td>
              <td>{{ profesor.status }}</td>
              <td>
                <button class="boton-verde" @click="editar(profesor)">Editar</button>
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
import ProfesoresManual from '@/components/ProfesoresManual.vue'
import CsvProfesores from '@/components/CsvProfesores.vue'
import EditarProfesores from '@/components/EditarProfesores.vue'

const mostrarFormulario = ref(false)
const mostrarCSV = ref(false)
const editarProfesores = ref(false)

const profesores = ref([])
const profesorSeleccionado = ref(null)

const busqueda = ref('')
const filtroEstado = ref('')

// Obtener profesores desde la API
const obtenerProfesores = async () => {
  try {
    const response = await axios.get('/api/profesores')
    profesores.value = response.data
  } catch (error) {
    console.error('Error al obtener profesores:', error)
  }
}

// Computado para buscar y filtrar por estado
const profesoresFiltrados = computed(() => {
  return profesores.value.filter(p => {
    const coincideBusqueda =
      p.nombre_completo.toLowerCase().includes(busqueda.value.toLowerCase()) ||
      p.matricula.toLowerCase().includes(busqueda.value.toLowerCase())
    const coincideEstado = !filtroEstado.value || p.status === filtroEstado.value
    return coincideBusqueda && coincideEstado
  })
})

// Función para seleccionar profesor y mostrar modal de edición
const editar = (profesor) => {
  profesorSeleccionado.value = { ...profesor }
  editarProfesores.value = true
}

onMounted(obtenerProfesores)
</script>

