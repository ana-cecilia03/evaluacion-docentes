<template>
  <Menu>
    <main class="contenido-principal">
      <!-- Barra de acciones: búsqueda, filtro por materia, botones -->
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
          <ProfesoresManual @cerrar="mostrarFormulario = false" @guardado="obtenerProfesores" />
        </div>
      </div>

      <!-- Modal para carga CSV -->
      <div v-if="mostrarCSV" class="modal-csv">
        <div class="modal-content">
          <CsvProfesores @cerrar="mostrarCSV = false" @guardado="obtenerProfesores" />
        </div>
      </div>

      <!-- Modal para actualizar -->
      <div v-if="editarProfesores" class="modal-csv">
        <div class="modal-content">
          <EditarProfesores
            :profesor="profesorSeleccionado"
            @cerrar="editarProfesores = false"
            @actualizado="() => { editarProfesores = false; obtenerProfesores() }"
          />
        </div>
      </div>

      <!-- Tabla con profesores cargados desde la BD -->
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
                <button class="btn-edit" @click="editar(profesor)">Editar</button>
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

// Layout y componentes modales
import Menu from '@/layouts/Menu.vue'
import ProfesoresManual from '@/components/ProfesoresManual.vue'
import CsvProfesores from '@/components/CsvProfesores.vue'
import EditarProfesores from '@/components/EditarProfesores.vue'

// Estados de modales
const mostrarFormulario = ref(false)
const mostrarCSV = ref(false)
const editarProfesores = ref(false)

// Lista de profesores y seleccionado para editar
const profesores = ref([])
const profesorSeleccionado = ref(null)

// Obtener todos los profesores desde la API
const obtenerProfesores = async () => {
  try {
    const response = await axios.get('/api/profesores')
    profesores.value = response.data
  } catch (error) {
    console.error('Error al obtener profesores:', error)
  }
}

// Función para iniciar edición
const editar = (profesor) => {
  profesorSeleccionado.value = { ...profesor }
  editarProfesores.value = true
}

// Al montar el componente, cargar profesores
onMounted(obtenerProfesores)
</script>

<style src="@/../css/Registros.css"></style>
