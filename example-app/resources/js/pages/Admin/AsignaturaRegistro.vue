<template>
  <Menu>
    <main class="contenido-principal">
      <!-- Barra de acciones: búsqueda y botones -->
      <div class="actions">
        <input type="text" v-model="busqueda" placeholder="Buscar materia" />

        <button class="btn-register" @click="mostrarFormulario = true">Registrar Materia Manual</button>
        <button class="btn-upload" @click="mostrarCSV = true">Cargar CSV</button>
      </div>

      <!-- Modal para formulario manual -->
      <div v-if="mostrarFormulario" class="modal-overlay">
        <div class="modal-content">
          <AsignaturasManual @cerrar="cerrarFormulario" @guardado="cargarMaterias" />
        </div>
      </div>

      <!-- Modal para carga CSV -->
      <div v-if="mostrarCSV" class="modal-csv">
        <div class="modal-content">
          <CsvAsignatura @cerrar="cerrarCSV" @guardado="cargarMaterias" />
        </div>
      </div>

      <!-- Modal para actualizar -->
      <div v-if="editarMateria" class="modal-overlay">
        <div class="modal-content">
          <EditarAsignaturas :materia="materiaSeleccionada" @cerrar="cerrarEditar" @actualizado="cargarMaterias" />
        </div>
      </div>

      <!-- Tabla de asignaturas -->
      <div class="table-wrapper">
        <table>
          <thead>
            <tr>
              <th>Clave</th>
              <th>Nombre de la Materia</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="materia in materiasFiltradas" :key="materia.id_materia">
              <td>{{ materia.clave }}</td>
              <td>{{ materia.nombre_materia }}</td>
              <td>
                <button class="btn-edit" @click="abrirEditar(materia)">Editar</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </main>
  </Menu>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

// Componentes
import Menu from '@/layouts/Menu.vue'
import AsignaturasManual from '@/components/AsignaturasManual.vue'
import CsvAsignatura from '@/components/CsvAsignatura.vue'
import EditarAsignaturas from '@/components/EditarAsignaturas.vue'

// Estados
const mostrarFormulario = ref(false)
const mostrarCSV = ref(false)
const editarMateria = ref(false)
const materiaSeleccionada = ref(null)
const materias = ref([])
const busqueda = ref('')

// Cargar materias desde API
const cargarMaterias = async () => {
  try {
    const response = await axios.get('/api/materias')
    materias.value = response.data
  } catch (error) {
    console.error('Error al cargar materias:', error)
  }
}

// Filtro por nombre o clave
const materiasFiltradas = computed(() => {
  return materias.value.filter(m =>
    m.nombre_materia.toLowerCase().includes(busqueda.value.toLowerCase()) ||
    m.clave.toLowerCase().includes(busqueda.value.toLowerCase())
  )
})

// Abrir modal de edición
const abrirEditar = (materia) => {
  materiaSeleccionada.value = { ...materia }
  editarMateria.value = true
}

// Cerrar modales
const cerrarFormulario = () => mostrarFormulario.value = false
const cerrarCSV = () => mostrarCSV.value = false
const cerrarEditar = () => editarMateria.value = false

// Al montar
onMounted(() => {
  cargarMaterias()
})
</script>

<style src="@/../css/Registros.css"></style>
