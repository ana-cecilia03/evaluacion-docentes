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
  router.visit(`/alumno/evaluar/${m.relacion_id}`)
}

// Carga las materias disponibles para evaluación desde el backend
const cargar = async () => {
  try {
    const { data } = await axios.get('/api/alumno/materias-por-evaluar')
    materias.value = data.materias
  } catch (error) {
    console.error('Error al cargar materias:', error)
  }
}

// Ejecuta la carga al montar el componente
onMounted(cargar)
</script>

<style src="@/../css/materias.css"></style>
<style src="@/../css/botones.css"></style>
