<template>
  <Menu>
    <main class="contenido-principal">
      <!-- Barra de acciones: búsqueda, filtro por estado, botones -->
      <div class="actions">
        <input type="text" placeholder="Buscar por nombre o ID" />
        <select>
          <option value="">Estado</option>
          <option>Activo</option>
          <option>Inactivo</option>
        </select>
        <button class="btn-register" @click="mostrarFormulario = true">Registrar Profesor Manual</button>
        <button class="btn-upload" @click="mostrarCSV = true">Cargar CSV</button>
      </div>

      <!-- Modal para formulario manual -->
      <div v-if="mostrarFormulario" class="modal-overlay">
        <div class="modal-content">
          <ProfesoresManual @cerrar="mostrarFormulario = false" />
        </div>
      </div>

      <!-- Modal para carga CSV -->
      <div v-if="mostrarCSV" class="modal-csv">
        <div class="modal-content">
          <CsvProfesores @cerrar="mostrarCSV = false" />
        </div>
      </div>

      <!-- Modal para actualizar -->
      <div v-if="editarProfesores" class="modal-csv">
        <div class="modal-content">
          <EditarProfesores :profesor="profesorSeleccionado" @cerrar="editarProfesores = false" />
        </div>
      </div>

      <!-- Tabla con profesores desde backend -->
      <div class="table-wrapper">
        <table>
          <thead>
            <tr>
              <th>Matrícula</th>
              <th>Nombre</th>
              <th>Curp</th>
              <th>Estado</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="profesor in profesores" :key="profesor.id_profesor">
              <td>{{ profesor.matricula }}</td>
              <td>{{ profesor.nombre_completo }}</td>
              <td>{{ profesor.curp }}</td>
              <td>{{ profesor.status }}</td>
              <td>
                <button class="btn-edit" @click="abrirEditar(profesor)">Editar</button>
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
import ProfesoresManual from '@/components/ProfesoresManual.vue'
import CsvProfesores from '@/components/CsvProfesores.vue'
import EditarProfesores from '@/components/EditarProfesores.vue'

// Estados para modales
const mostrarFormulario = ref(false)
const mostrarCSV = ref(false)
const editarProfesores = ref(false)

// Datos de profesores
const profesores = ref([])
const profesorSeleccionado = ref(null)

// Cargar profesores desde backend
const obtenerProfesores = async () => {
  try {
    const response = await axios.get('/api/profesores')
    profesores.value = response.data
  } catch (error) {
    console.error('Error al obtener profesores:', error)
  }
}

// Abrir modal de edición
const abrirEditar = (profesor) => {
  profesorSeleccionado.value = { ...profesor }
  editarProfesores.value = true
}

// Obtener datos al cargar componente
onMounted(obtenerProfesores)
</script>

<style src="@/../css/Registros.css"></style>
