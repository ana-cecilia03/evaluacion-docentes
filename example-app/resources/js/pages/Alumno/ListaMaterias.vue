<template>
  <MenuAlumno>
    <main class="contenido-principal">
      <!-- Campo de búsqueda para filtrar materias -->
      <div class="actions">
        <input v-model="busqueda" placeholder="Buscar por nombre o materia" />
      </div>

      <!-- Tabla que muestra la lista de materias con su respectivo profesor -->
      <table>
        <thead>
          <tr>
            <th>Materia</th>
            <th>Profesor</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <!-- Itera sobre materias filtradas -->
          <tr v-for="m in materiasFiltradas" :key="m.relacion_id">
            <td>{{ m.materia_nombre }}</td>
            <td>{{ m.profesor_nombre }}</td>
            <td>
              <button class="boton-azul" @click="evaluarMateria(m)">Evaluar</button>
            </td>
          </tr>
          <!-- Mensaje cuando no hay resultados -->
          <tr v-if="materiasFiltradas.length === 0">
            <td colspan="3">Sin profesores por evaluar</td>
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
