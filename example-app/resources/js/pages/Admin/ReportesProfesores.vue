<template>
  <Menu>
    <main class="contenido-principal">
      <div class="actions">
        <input
          type="text"
          v-model="busqueda"
          placeholder="Buscar por nombre o matrícula"
        />
        <select v-model="filtroCargo">
          <option value="">Filtrar por cargo</option>
          <option value="PTC">PTC</option>
          <option value="PA">PA</option>
        </select>
      </div>

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
                <button
                  :class="profesor.evaluado ? 'boton-gris' : 'boton-azul'"
                  @click="evaluarProfesor(profesor)"
                >
                  {{ profesor.evaluado ? 'Evaluado' : 'Evaluar' }}
                </button>
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
import { router } from '@inertiajs/vue3'
import axios from '@/lib/axios'
import Menu from '@/layouts/Menu.vue'

// Estado
const profesores = ref([])   // vendrá con campo booleano "evaluado"
const busqueda = ref('')
const filtroCargo = ref('')

// Cargar profesores activos (protegido con Sanctum)
onMounted(async () => {
  try {
    const { data } = await axios.get('/profesores/activos') // ← sin /api (baseURL ya es /api)
    profesores.value = data
  } catch (e) {
    console.error('Error cargando activos:', e?.response?.status, e?.response?.data || e)
    alert('No se pudieron cargar los profesores.')
  }
})

// Filtrado por texto y cargo
const profesoresFiltrados = computed(() => {
  const term = busqueda.value.toLowerCase()
  return profesores.value.filter((p) => {
    const matchBusqueda =
      (p.nombre_completo || '').toLowerCase().includes(term) ||
      (p.matricula || '').toString().includes(busqueda.value)
    const matchCargo = filtroCargo.value ? p.cargo === filtroCargo.value : true
    return matchBusqueda && matchCargo
  })
})

// Navegar a la evaluación correspondiente
//  Ya no bloqueamos si está evaluado: sigue navegando aunque se pinte en gris
function evaluarProfesor(profesor) {
  const ruta = profesor.cargo === 'PA'
    ? `/evaluacionProfesorPA/${profesor.id_profesor}`
    : `/evaluacionProfesorPTC/${profesor.id_profesor}`

  router.visit(ruta, { method: 'get', preserveState: false })
}
</script>


<style src="@/../css/Registros.css"></style>
<style src="@/../css/botones.css"></style>

<style>
.fila-evaluado {
  background-color: #f5f5f5;
  opacity: 0.7;
}
.boton-gris {
  background-color: #bdbdbd !important;
  color: #fff !important;
  cursor: not-allowed !important;
}
</style>
