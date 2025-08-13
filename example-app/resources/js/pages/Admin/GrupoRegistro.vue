<template>
  <Menu>
    <main class="contenido-principal">
      <!-- Barra de acciones: búsqueda, filtro, botones de registro -->
      <div class="actions">
        <input type="text" placeholder="Buscar grupo..." />
        <button class="btn-register" @click="mostrarFormulario = true">Registrar Grupo Manual</button>
        <button class="btn-upload" @click="mostrarCSV = true">Cargar CSV</button>
      </div>

      <!-- Modal para formulario manual -->
      <div v-if="mostrarFormulario" class="modal-overlay">
        <div class="modal-content">
          <GruposManual @cerrar="cerrarFormulario" @guardado="obtenerGrupos" />
        </div>
      </div>

      <!-- Modal para carga CSV -->
      <div v-if="mostrarCSV" class="modal-csv">
        <div class="modal-content">
          <CsvGrupos @cerrar="cerrarCSV" @guardado="obtenerGrupos" />
        </div>
      </div>

      <!-- Modal para actualizar grupo -->
      <div v-if="editarGrupos" class="modal-overlay">
        <div class="modal-content">
          <EditarGrupos
            :grupo="grupoSeleccionado"
            @cerrar="cerrarEdicion"
            @actualizado="obtenerGrupos"
          />
        </div>
      </div>

      <!-- Tabla con datos desde la base de datos -->
      <div class="table-wrapper">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Nombre Grupo</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="grupo in grupos" :key="grupo.id_grupo">
              <td>{{ grupo.id_grupo }}</td>
              <td>{{ grupo.nombre }}</td>
              <td>
                <button class="boton-verde" @click="abrirModalEditar(grupo)">Editar</button>
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

// Componentes
import Menu from '@/layouts/Menu.vue'
import GruposManual from '@/components/GruposManual.vue'
import CsvGrupos from '@/components/CsvGrupos.vue'
import EditarGrupos from '@/components/EditarGrupos.vue'

// Estados de modales
const mostrarFormulario = ref(false)
const mostrarCSV = ref(false)
const editarGrupos = ref(false)

// Datos
const grupos = ref([])
const grupoSeleccionado = ref(null)

// Función para obtener grupos desde la API
const obtenerGrupos = async () => {
  try {
    const response = await axios.get('/api/grupos')
    grupos.value = response.data
  } catch (error) {
    console.error('Error al obtener grupos:', error)
  }
}

// Mostrar modal de edición
const abrirModalEditar = (grupo) => {
  grupoSeleccionado.value = grupo
  editarGrupos.value = true
}

// Cerrar modales
const cerrarFormulario = () => (mostrarFormulario.value = false)
const cerrarCSV = () => (mostrarCSV.value = false)
const cerrarEdicion = () => (editarGrupos.value = false)

// Cargar grupos al montar
onMounted(obtenerGrupos)
</script>
