<template>
  <teleport to="body">
    <div class="modal-overlay" @click.self="cerrar">
      <div class="modal-content">
        <h2>Actualizar relación</h2>

        <!-- Carrera (no editable, deshabilitada) -->
        <div class="form-group">
          <label for="carrera">Carrera:</label>
          <select
            id="carrera"
            v-model="form.carrera_nombre"
            disabled
            style="background-color: #e0e0e0; cursor: not-allowed;"
          >
            <option
              v-for="c in carreras"
              :key="c.nombre_carrera"
              :value="c.nombre_carrera"
            >
              {{ c.nombre_carrera }}
            </option>
          </select>
        </div>

        <!-- Cuatrimestre -->
        <div class="form-group">
          <label for="cuatri">Cuatrimestre:</label>
          <select id="cuatri" v-model="form.cuatri_num">
            <option disabled value="">Seleccione cuatrimestre</option>
            <option v-for="n in 12" :key="n" :value="n">{{ n }}</option>
          </select>
          <span class="error" v-if="errores.cuatri_num">{{ errores.cuatri_num }}</span>
        </div>

        <!-- Materia -->
        <div class="form-group">
          <label for="materia">Materia:</label>
          <select id="materia" v-model="form.materia_nombre">
            <option disabled value="">Seleccione una materia</option>
            <option
              v-for="m in materias"
              :key="m.nombre_materia"
              :value="m.nombre_materia"
            >
              {{ m.nombre_materia }}
            </option>
          </select>
          <span class="error" v-if="errores.materia_nombre">{{ errores.materia_nombre }}</span>
        </div>

        <!-- Botones -->
        <div class="button-group">
          <button class="editar-rojo" @click="cerrar">Cancelar</button>
          <button class="editar-verde" @click="guardar">Actualizar</button>
        </div>
      </div>
    </div>
  </teleport>
</template>

<script setup>
import { reactive, ref, watch, onMounted } from 'vue'
import axios from 'axios'

const props = defineProps({
  relacion: Object
})

const emit = defineEmits(['cerrar', 'guardar'])

const form = reactive({
  id_mat_cuatri_car: null,
  carrera_nombre: '',
  cuatri_num: '',
  materia_nombre: ''
})

const errores = reactive({
  carrera_nombre: '',
  cuatri_num: '',
  materia_nombre: ''
})

const carreras = ref([])
const materias = ref([])

onMounted(async () => {
  const [resCarreras, resMaterias] = await Promise.all([
    axios.get('/api/carreras/nombres'),
    axios.get('/api/materias/nombres')
  ])
  carreras.value = resCarreras.data
  materias.value = resMaterias.data
})

// Rellenar form con datos cuando se recibe la prop
watch(() => props.relacion, (nuevo) => {
  if (nuevo) {
    form.id_mat_cuatri_car = nuevo.id_mat_cuatri_car
    form.carrera_nombre = nuevo.carrera_nombre
    form.cuatri_num = nuevo.cuatri_num
    form.materia_nombre = nuevo.materia_nombre
    Object.keys(errores).forEach(k => errores[k] = '')
  }
}, { immediate: true })

function cerrar() {
  emit('cerrar')
}

async function guardar() {
  try {
    Object.keys(errores).forEach(k => errores[k] = '')

    await axios.put(`/api/mat-cuatri-car/${form.id_mat_cuatri_car}`, form)
    emit('guardar')
  } catch (error) {
    if (error.response && error.response.status === 422) {
      const fields = error.response.data.errors
      Object.keys(fields).forEach(k => errores[k] = fields[k][0])
    } else {
      alert('Error al actualizar la relación')
    }
  }
}
</script>

<style src="@/../css/Editar.css"></style>


