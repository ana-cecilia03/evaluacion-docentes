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
import axios from 'axios'

const materias = ref([])       // Lista de materias asignadas al alumno
const busqueda = ref('')       // Texto para búsqueda por nombre o profesor
const error = ref(null)
const cargando = ref(true)
const enviandoId = ref(null)   // Para mostrar "Enviando..." en botón

// Lista reactiva filtrada según el texto ingresado
const materiasFiltradas = computed(() =>
  materias.value.filter(m =>
    m.materia_nombre.toLowerCase().includes(busqueda.value.toLowerCase()) ||
    m.profesor_nombre.toLowerCase().includes(busqueda.value.toLowerCase())
  )
)

// Redirige al formulario de evaluación del profesor según la relación
function evaluarMateria(m) {
  if (!m.relacion_id) {
    console.error('relacion_id no definido en la materia:', m)
    return
  }
  enviandoId.value = m.relacion_id
  router.visit(`/alumno/evaluar/${m.relacion_id}`)
}

// Carga las materias disponibles para evaluación desde el backend
const cargar = async () => {
  try {
    const alumno = JSON.parse(localStorage.getItem('alumno'))
    if (!alumno || !alumno.id_alumno) {
      error.value = 'No se encontró información del alumno'
      cargando.value = false
      return
    }

    const { data } = await axios.get('/api/alumno/materias', {
      params: { id: alumno.id_alumno }
    })

    materias.value = data.materias
  } catch (err) {
    console.error('Error al cargar materias:', err)
    error.value = 'Ocurrió un error al cargar las materias'
  } finally {
    cargando.value = false
  }
}

// Ejecuta la carga al montar el componente
// Protección de ruta al montar componente
onMounted(() => {
  const alumno = localStorage.getItem('alumno')
  if (!alumno) {
    router.visit('/bienvenida')
    return
  }

  cargar()
})
</script>

