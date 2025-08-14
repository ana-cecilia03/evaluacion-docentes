<template>
  <MenuAlumno>
    <main class="contenido-principal">
      <!-- Acciones -->
      <div class="actions">
        <input v-model="busqueda" placeholder="Buscar por nombre o materia" />
      </div>

      <!-- Tabla -->
      <table>
        <thead>
          <tr>
            <th>Materia</th>
            <th>Profesor</th>
            <th style="width: 1%"></th>
          </tr>
        </thead>

        <tbody>
          <!-- Estado: cargando -->
          <tr v-if="cargando">
            <td colspan="3">Cargando materias…</td>
          </tr>

          <!-- Estado: error -->
          <tr v-else-if="error">
            <td colspan="3">{{ error }}</td>
          </tr>

          <!-- Sin resultados -->
          <tr v-else-if="materiasFiltradas.length === 0">
            <td colspan="3">Sin profesores por evaluar</td>
          </tr>

          <!-- Lista -->
          <tr v-else v-for="m in materiasFiltradas" :key="m.relacion_id">
            <td>{{ m.materia_nombre }}</td>
            <td>{{ m.profesor_nombre }}</td>
            <td>
              <button
                :class="m.evaluado ? 'boton-gris' : 'boton-azul'"
                :disabled="m.evaluado || enviandoId === m.relacion_id"
                @click="evaluarMateria(m)"
                :aria-disabled="m.evaluado || enviandoId === m.relacion_id"
                :title="m.evaluado ? 'Ya evaluado' : 'Evaluar'"
              >
                <span v-if="enviandoId === m.relacion_id">Enviando…</span>
                <span v-else>{{ m.evaluado ? 'Evaluado' : 'Evaluar' }}</span>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </main>
  </MenuAlumno>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import MenuAlumno from '@/layouts/MenuAlumno.vue'
import { router } from '@inertiajs/vue3'
import api from '@/lib/axios' // usa tu instancia con baseURL '/api'

// state
const materias = ref([])
const busqueda = ref('')
const error = ref(null)
const cargando = ref(true)
const enviandoId = ref(null)

// filtro seguro (null-safe)
const materiasFiltradas = computed(() =>
  materias.value.filter(m => {
    const materia = (m.materia_nombre || '').toLowerCase()
    const profe = (m.profesor_nombre || '').toLowerCase()
    const q = busqueda.value.toLowerCase()
    return materia.includes(q) || profe.includes(q)
  })
)

// navegación a evaluación
function evaluarMateria(m) {
  if (!m.relacion_id) {
    console.error('relacion_id no definido en la materia:', m)
    return
  }
  enviandoId.value = m.relacion_id
  router.visit(`/alumno/evaluar/${m.relacion_id}`)
}

// carga (usa endpoint público temporal con ?id=)
const cargar = async () => {
  cargando.value = true
  error.value = null
  try {
    const alumno = JSON.parse(localStorage.getItem('alumno') || 'null')
    const id = alumno?.id_alumno ?? alumno?.id
    if (!alumno || !id) {
      router.visit('/bienvenida')
      return
    }

    // ENDPOINT TEMPORAL (público): /api/alumno/materias-public?id=<id_alumno>
    const { data } = await api.get('/alumno/materias-public', { params: { id } })

    materias.value = data?.materias ?? data?.data ?? []
  } catch (err) {
    console.error('Error al cargar materias:', {
      status: err.response?.status,
      data: err.response?.data
    })
    error.value = 'Ocurrió un error al cargar las materias'
  } finally {
    cargando.value = false
  }
}

// protección + carga
onMounted(() => {
  const alumno = localStorage.getItem('alumno')
  if (!alumno) {
    router.visit('/bienvenida')
    return
  }
  cargar()
})
</script>
