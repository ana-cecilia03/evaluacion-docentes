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
                <button class="boton-azul" @click="evaluarProfesor(profesor)">
                  Evaluar
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
import axios from 'axios'
import Menu from '@/layouts/Menu.vue'

// Lista de profesores activos
const profesores = ref([])

// Campos para filtrar
const busqueda = ref('')
const filtroCargo = ref('')

// 🔁 Al montar el componente, se solicita la lista de profesores activos al backend
onMounted(async () => {
  const res = await axios.get('/api/profesores/activos')
  profesores.value = res.data
})

// 🎯 Computed que filtra por texto de búsqueda o por cargo
const profesoresFiltrados = computed(() => {
  return profesores.value.filter((prof) => {
    const matchBusqueda =
      prof.nombre_completo.toLowerCase().includes(busqueda.value.toLowerCase()) ||
      prof.matricula.includes(busqueda.value)

    const matchCargo = filtroCargo.value
      ? prof.cargo === filtroCargo.value
      : true

    return matchBusqueda && matchCargo
  })
})

// 🧭 Función que redirige a la vista de evaluación correspondiente
// Usa router.visit() de Inertia para enviar el ID del profesor como prop
function evaluarProfesor(profesor) {
  const ruta = profesor.cargo === 'PA'
    ? `/evaluacionProfesorPA/${profesor.id_profesor}`
    : `/evaluacionProfesorPTC/${profesor.id_profesor}`

  router.visit(ruta, {
    method: 'get',
    preserveState: false
  })
}
</script>

<style src="@/../css/Registros.css"></style>
<style src="@/../css/botones.css"></style>
