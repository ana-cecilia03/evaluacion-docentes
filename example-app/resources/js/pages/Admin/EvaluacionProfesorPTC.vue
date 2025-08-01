<template>
  <Menu>
    <main class="contenido-principal evaluacion-page">
      <!-- CABECERA -->
      <header class="encabezado-evaluacion">
        <h1 class="titulo">Evaluación de Profesores PTC</h1>

        <div class="acciones">
          <div class="download-wrapper">
            <button class="boton-verde" @click="toggleDropdown">
              Descargar
            </button>
            <ul v-if="showDownload" class="dropdown">
              <li @click="download('pdf')">PDF</li>
              <li @click="download('xlsx')">Excel</li>
            </ul>
          </div>
        </div>

        <div class="datos-grid">
          <div class="col-izq">
            <div class="campo" v-for="(label, key) in camposFormulario" :key="key">
              <label>{{ label }}:</label>
              <input :value="form[key]" type="text" disabled />
            </div>
          </div>

          <div class="col-mid">
            <div class="box-calif">
              <span class="titulo-box">Calificación I <br />Resp. PE</span>
              <input
                :value="promedio.toFixed(1)"
                type="text"
                readonly
                class="input-calif"
                style="background-color: white; border: none; font-weight: bold; font-size: 1.2rem; text-align: center; pointer-events: none;"
              />
            </div>
            <div class="box-calif">
              <span class="titulo-box">Calificación II <br />ESTUDIANTE</span>
              <input
                v-model.number="califII"
                type="number"
                step="0.1"
                min="0"
                max="10"
                class="input-calif"
              />
            </div>
          </div>

          <div class="col-der">
            <div class="total-box">
              <span class="total">{{ calificacionFinal.toFixed(1) }}</span>
            </div>
          </div>
        </div>
      </header>

      <!-- TABLA -->
      <div class="table-wrapper">
        <table class="tabla-evaluacion">
          <thead>
            <tr>
              <th>Factor</th>
              <th>Definición</th>
              <th>Calificación</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="pregunta in preguntas" :key="pregunta.id">
              <td>{{ pregunta.factor }}</td>
              <td>{{ pregunta.definicion }}</td>
              <td>
                <input
                  type="number"
                  v-model.number="pregunta.calificacion"
                  min="1"
                  max="5"
                  step="0.1"
                  class="input-calif"
                  @input="limitarCalificacion(pregunta)"
                />
              </td>
            </tr>
          </tbody>

          <tfoot>
            <tr>
              <td colspan="2" class="comentario-wrapper">
                <textarea
                  v-model="comentario"
                  placeholder="Escribe un comentario"
                  class="comentario"
                ></textarea>
              </td>
              <td class="resumen-wrapper">
                <table class="tabla-resumen">
                  <tbody>
                    <tr>
                      <td rowspan="2" class="sub-total-titulo">Sub.<br />total</td>
                      <td>{{ promedio.toFixed(1) }}</td>
                      <td>{{ (promedio / 2).toFixed(1) }}</td>
                    </tr>
                    <tr>
                      <td colspan="2" class="calif-final-titulo">Calificación:</td>
                    </tr>
                    <tr>
                      <td colspan="3" class="calif-final">{{ calificacionFinal.toFixed(1) }}</td>
                    </tr>
                  </tbody>
                </table>
              </td>
            </tr>
          </tfoot>
        </table>
      </div>

      <!-- FIRMA -->
      <section class="firma">
        <strong>Elaborado por: </strong>
        <span class="linea-firma"></span>
        <div class="nombre-firma">{{ form.evaluador }}</div>
      </section>
    </main>
  </Menu>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import axios from 'axios'
import Menu from '@/layouts/Menu.vue'

// 🟢 RECIBE ID DEL PROFESOR A EVALUAR
const props = defineProps({
  id: { type: Number, required: true }
})

const showDownload = ref(false)
const califII = ref(0)
const comentario = ref('')

const form = reactive({
  nombre: '',
  puesto: '',
  evaluador: '',
  periodo: '2023'
})

const camposFormulario = {
  nombre: 'Nombre',
  puesto: 'Puesto',
  evaluador: 'Evaluador',
  periodo: 'Periodo'
}

const preguntas = ref([])

// 🔹 PREGUNTAS TIPO PTC
const obtenerPreguntas = async () => {
  try {
    const res = await axios.get('/api/evaluaciones/preguntas-ptc')
    preguntas.value = res.data.map(p => ({
      ...p,
      calificacion: 0
    }))
  } catch (error) {
    console.error('Error al obtener preguntas:', error)
  }
}

// 🔹 DATOS DEL EVALUADOR (administrador)
const obtenerEvaluador = async () => {
  try {
    const res = await axios.get('/api/evaluador')
    form.evaluador = res.data.evaluador || 'Sin permiso'
  } catch (error) {
    console.error('Error al obtener evaluador:', error)
    form.evaluador = 'Error'
  }
}

// 🔹 DATOS DEL PROFESOR EVALUADO
const cargarDatosProfesor = async () => {
  try {
    const res = await axios.get(`/api/profesores/${props.id}`)
    form.nombre = res.data.nombre_completo
    form.puesto = res.data.cargo
  } catch (error) {
    console.error('Error al cargar profesor:', error)
  }
}

// 🔸 PROMEDIOS
const promedio = computed(() => {
  const total = preguntas.value.reduce((sum, p) => sum + p.calificacion, 0)
  return preguntas.value.length ? total / preguntas.value.length : 0
})
const calificacionFinal = computed(() => promedio.value)

// 🔸 ACCIONES
function toggleDropdown() {
  showDownload.value = !showDownload.value
}
function download(format) {
  console.log(`Descargar en formato: ${format}`)
}
function limitarCalificacion(pregunta) {
  if (pregunta.calificacion > 5) pregunta.calificacion = 5
  else if (pregunta.calificacion < 1) pregunta.calificacion = 1
}

// 🔸 MONTAJE INICIAL
onMounted(() => {
  obtenerPreguntas()
  cargarDatosProfesor()
  obtenerEvaluador()
})
</script>

<style src="@/../css/botones.css"></style>
<style src="@/../css/EvaluacionProfesores.css"></style>
