<template>
  <Menu>
    <main class="contenido-principal">
      <!-- Barra de acciones: búsqueda, filtro y botones -->
      <div class="actions">
        <!-- Búsqueda por texto -->
        <input type="text" placeholder="Buscar carrera..." v-model="busqueda" />

        <!-- Filtro por clave de carrera -->
        <select v-model="filtroCarrera">
          <option value="">Filtrar por carrera</option>
          <option 
            v-for="carrera in carreras" 
            :key="carrera.id_carrera" 
            :value="carrera.clave"
          >
            {{ carrera.clave }} - {{ carrera.nombre_carrera }}
          </option>
        </select>

        <!-- Botón para mostrar formulario -->
        <button class="btn-register" @click="mostrarFormulario = true">Registrar Carrera</button>
      </div>

      <!-- Modal para registrar carrera -->
      <div v-if="mostrarFormulario" class="modal-overlay">
        <div class="modal-content">
          <CarrerasManual 
            @cerrar="mostrarFormulario = false" 
            @guardado="cargarCarreras" 
          />
        </div>
      </div>

      <!-- Modal para editar carrera -->
      <div v-if="editarCarreras" class="modal-overlay">
        <div class="modal-content">
          <EditarCarreras 
            :carrera="carreraSeleccionada"
            @cerrar="editarCarreras = false"
            @actualizado="cargarCarreras"
          />
        </div>
      </div>

      <!-- Tabla dinámica de carreras -->
      <div class="table-wrapper">
        <table>
          <thead>
            <tr>
              <th>Clave</th>
              <th>Nombre</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="carrera in carrerasFiltradas" :key="carrera.id_carrera">
              <td>{{ carrera.clave }}</td>
              <td>{{ carrera.nombre_carrera }}</td>
              <td>
                <button class="boton-verde" @click="editar(carrera)">Editar</button>
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
import CarrerasManual from '@/components/CarrerasManual.vue'
import EditarCarreras from '@/components/EditarCarreras.vue'

// Estado reactivo
const mostrarFormulario = ref(false)
const editarCarreras = ref(false)
const carreraSeleccionada = ref(null)
const carreras = ref([])
const busqueda = ref('')
const filtroCarrera = ref('') // Filtro del <select>

// Cargar carreras desde el backend (GET /api/carreras)
const cargarCarreras = async () => {
  try {
    const response = await axios.get('/api/carreras')
    carreras.value = response.data
  } catch (error) {
    console.error('Error al cargar carreras:', error)
  }
}

// Computed para aplicar filtros combinados
const carrerasFiltradas = computed(() => {
  return carreras.value.filter(carrera => {
    const coincideBusqueda =
      carrera.nombre_carrera.toLowerCase().includes(busqueda.value.toLowerCase()) ||
      carrera.clave.toLowerCase().includes(busqueda.value.toLowerCase())

    const coincideFiltro =
      filtroCarrera.value === '' || carrera.clave === filtroCarrera.value

    return coincideBusqueda && coincideFiltro
  })
})

// Función para abrir el modal con carrera seleccionada
function editar(carrera) {
  carreraSeleccionada.value = carrera
  editarCarreras.value = true
}

// Al montar el componente, cargar historial
onMounted(() => {
  cargarCarreras()
})
</script>
<style src="@css/global.css" lang="css"></style>