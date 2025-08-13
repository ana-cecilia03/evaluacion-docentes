<template>
  <teleport to="body">
    <div class="modal-overlay" @click.self="cerrar">
      <div class="modal-content">
        <h2>Actualizar relación</h2>

        <!-- Profesor -->
        <div class="form-group">
          <label for="profesor">Profesor:</label>
          <select id="profesor" v-model="form.profesor_id">
            <option disabled value="">Seleccione un profesor</option>
            <option
              v-for="p in profesores"
              :key="p.id_profesor"
              :value="p.id_profesor"
            >
              {{ p.nombre_completo }}
            </option>
          </select>
        </div>

        <!-- Periodo -->
        <div class="form-group">
          <label for="periodo">Periodo:</label>
          <select id="periodo" v-model="form.periodo_id">
            <option disabled value="">Seleccione un periodo</option>
            <option
              v-for="p in periodos"
              :key="p.id_periodo"
              :value="p.id_periodo"
            >
              {{ p.nombre_periodo }}
            </option>
          </select>
        </div>

        <!-- Carrera -->
        <div class="form-group">
          <label for="carrera">Carrera:</label>
          <select id="carrera" v-model="form.carrera_nombre">
            <option disabled value="">Seleccione una carrera</option>
            <option
              v-for="c in carreras"
              :key="c.nombre_carrera"
              :value="c.nombre_carrera"
            >
              {{ c.nombre_carrera }}
            </option>
          </select>
        </div>

        <!-- Materia -->
        <div class="form-group">
          <label for="materia">Materia:</label>
          <select id="materia" v-model="form.id_mat_cuatri_car">
            <option disabled value="">Seleccione una materia</option>
            <option
              v-for="m in materias"
              :key="m.id_mat_cuatri_car"
              :value="m.id_mat_cuatri_car"
            >
              {{ m.materia_nombre }} ({{ m.cuatri_num }})
            </option>
          </select>
        </div>

        <!-- Grupo -->
        <div class="form-group">
          <label for="grupo">Grupo:</label>
          <select id="grupo" v-model="form.grupo_id">
            <option disabled value="">Seleccione un grupo</option>
            <option
              v-for="g in grupos"
              :key="g.id_grupo"
              :value="g.id_grupo"
            >
              {{ g.nombre }}
            </option>
          </select>
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

const emit = defineEmits(['cerrar', 'guardar'])

const props = defineProps({
  relacion: Object
})

const profesores = ref([])
const periodos = ref([])
const carreras = ref([])
const materias = ref([])
const grupos = ref([])

const form = reactive({
  profesor_id: '',
  periodo_id: '',
  carrera_nombre: '',
  id_mat_cuatri_car: '',
  grupo_id: '',
  modified_by: 'admin'
})

// Cargar catálogos al montar
onMounted(async () => {
  const [resProf, resPer, resCar, resGru] = await Promise.all([
    axios.get('/api/selects/profesores'),
    axios.get('/api/selects/periodos'),
    axios.get('/api/carreras/nombres'),
    axios.get('/api/selects/grupos')
  ])
  profesores.value = resProf.data
  periodos.value = resPer.data
  carreras.value = resCar.data
  grupos.value = resGru.data
})

// Al cambiar carrera, cargar materias relacionadas
watch(() => form.carrera_nombre, async (carrera) => {
  if (!carrera) return
  const res = await axios.get(`/api/materias/por-carrera/${encodeURIComponent(carrera)}`)
  materias.value = res.data
})

// Precargar datos del formulario
watch(() => props.relacion, (rel) => {
  if (rel) {
    form.profesor_id = rel.profesor_id
    form.periodo_id = rel.periodo_id
    form.carrera_nombre = rel.mat_cuatri_car?.carrera_nombre || ''
    form.id_mat_cuatri_car = rel.id_mat_cuatri_car
    form.grupo_id = rel.grupo_id
  }
}, { immediate: true })

function cerrar() {
  emit('cerrar')
}

function guardar() {
  emit('guardar', { ...form })
}
</script>

