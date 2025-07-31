<template>
  <Menu>
    <main class="contenido-principal">
      <!-- Filtros de búsqueda -->
      <div class="actions">
        <input
          type="text"
          v-model="busqueda"
          placeholder="Buscar por nombre o matrícula"
        />
        <select v-model="filtroCargo" class="filtro">
          <option value="">Filtrar por cargo</option>
          <option value="PTC">PTC</option>
          <option value="PA">PA</option>
        </select>
      </div>

      <!-- Tabla de profesores -->
      <div class="table-wrapper">
        <table>
          <thead>
            <tr>
              <th>Matrícula</th>
              <th>Nombre</th>
              <th>Cargo</th>
              <th>Estado</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="profesor in profesoresFiltrados"
              :key="profesor.id_profesor"
            >
              <td>{{ profesor.matricula }}</td>
              <td>{{ profesor.nombre_completo }}</td>
              <td>{{ profesor.cargo }}</td>
              <td>{{ profesor.status }}</td>
              <td>
                <button class="boton-azul">Evaluar</button>
              </td>
            </tr>
            <tr v-if="profesoresFiltrados.length === 0">
              <td colspan="5">No se encontraron resultados.</td>
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
import Menu from '@/layouts/Menu.vue'

const profesores = ref([])
const busqueda = ref('')
const filtroCargo = ref('')

// Obtener solo profesores activos desde el backend
onMounted(async () => {
  try {
    const response = await axios.get('/api/profesores/activos')
    profesores.value = response.data
  } catch (error) {
    console.error('Error al obtener profesores activos:', error)
  }
})

// Computed para aplicar filtros de búsqueda y cargo
const profesoresFiltrados = computed(() => {
  return profesores.value.filter((prof) => {
    const coincideBusqueda =
      prof.nombre_completo.toLowerCase().includes(busqueda.value.toLowerCase()) ||
      prof.matricula.includes(busqueda.value)

    const coincideCargo = filtroCargo.value
      ? prof.cargo === filtroCargo.value
      : true

    return coincideBusqueda && coincideCargo
  })
})
</script>

<style src="@/../css/Registros.css"></style>
<style src="@/../css/botones.css"></style>
