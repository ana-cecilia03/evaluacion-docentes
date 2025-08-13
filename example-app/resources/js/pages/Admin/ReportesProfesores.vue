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
              :class="{ 'fila-evaluado': profesor.evaluado }"
            >
              <td>{{ profesor.matricula }}</td>
              <td>{{ profesor.nombre_completo }}</td>
              <td>{{ profesor.cargo }}</td>
              <td>{{ profesor.status }}</td>
              <td>
                <button
                  :class="profesor.evaluado ? 'boton-gris' : 'boton-azul'"
                  @click="evaluarProfesor(profesor)"
                  :title="profesor.evaluado ? 'Ya evaluado en el periodo actual' : 'Evaluar ahora'"
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
import { ref, computed, onMounted, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import axios from '@/lib/axios'
import Menu from '@/layouts/Menu.vue'

// Estado base de la tabla
const profesores = ref([])   // se normaliza agregando booleano "evaluado"
const busqueda = ref('')
const filtroCargo = ref('')

// Periodo actual (mismo criterio que usas en vistas de evaluación)
const periodo = ref(new Date().getFullYear().toString())

// Carga inicial: profesores + marcado de evaluados
onMounted(async () => {
  try {
    // /api/profesores/activos (pública según tus rutas)
    const { data } = await axios.get('/profesores/activos')
    // Normaliza estructura y asegura "evaluado" para no romper el render
    profesores.value = (data || []).map(p => ({ ...p, evaluado: !!p.evaluado }))
    await marcarEvaluados()
  } catch (e) {
    console.error('Error cargando activos:', e?.response?.status, e?.response?.data || e)
    alert('No se pudieron cargar los profesores.')
  }
})

// Si cambia el periodo, volvemos a marcar evaluados
watch(periodo, () => marcarEvaluados())

// Marca cada profesor como evaluado consultando backend; usa localStorage como respaldo
async function marcarEvaluados() {
  if (!Array.isArray(profesores.value) || profesores.value.length === 0) return

  const tasks = profesores.value.map(async (p) => {
    // Fallback local: misma convención que en las vistas de evaluación (evaluado:TIPO:ID:PERIODO)
    const local = localStorage.getItem(`evaluado:${p.cargo}:${p.id_profesor}:${periodo.value}`) === '1'
    if (local) {
      p.evaluado = true
      return
    }

    // Llamada a backend para saber si ya existe evaluación en el periodo
    try {
      // /api/evaluaciones/estado (protegida). Si no hay auth, simplemente no modifica "evaluado".
      const { data } = await axios.get('/evaluaciones/estado', {
        params: { profesor_id: p.id_profesor, tipo: p.cargo, periodo: periodo.value }
      })
      p.evaluado = !!data?.evaluado
    } catch (e) {
      // Ante error (p. ej., 401 si no hay token), mantenemos el valor actual
    }
  })

  await Promise.allSettled(tasks)
}

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

// Navegar a la evaluación correspondiente (no se bloquea la navegación)
function evaluarProfesor(profesor) {
  const ruta = profesor.cargo === 'PA'
    ? `/evaluacionProfesorPA/${profesor.id_profesor}`
    : `/evaluacionProfesorPTC/${profesor.id_profesor}`

  router.visit(ruta, { method: 'get', preserveState: false })
}
</script>

<style src="@/../css/global.css"></style>

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
