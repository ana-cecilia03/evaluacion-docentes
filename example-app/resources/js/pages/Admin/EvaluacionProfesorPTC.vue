<template>
  <Menu>
    <main class="contenido-principal evaluacion-page">
      <header class="encabezado-evaluacion">
        <h1 class="titulo">Evaluación de Profesores PTC</h1>

        <div class="acciones acciones-botones">
          <div class="download-wrapper">
            <button class="boton-verde" @click="toggleDropdown">Descargar</button>
            <ul v-if="showDownload" class="dropdown">
              <li @click="downloadPDF">PDF</li>
              <li @click="downloadExcel">Excel</li>
            </ul>
          </div>

          <button
            :class="evaluado ? 'boton-gris' : 'boton-verde'"
            :disabled="evaluado"
            @click="guardarEvaluacion"
          >
            {{ evaluado ? 'Evaluado' : 'Evaluar' }}
          </button>
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
              <span class="titulo-box">Calificación I <br />Resp. PE (0–5)</span>
              <input
                :value="promedio.toFixed(1)"
                type="text"
                readonly
                class="input-calif"
                style="background-color: white; border: none; font-weight: bold; font-size: 1.2rem; text-align: center; pointer-events: none;"
              />
            </div>
            <div class="box-calif">
              <span class="titulo-box">Calificación II <br />ESTUDIANTE (0–5)</span>
              <input
                v-model.number="califII"
                type="number"
                step="0.1"
                min="0"
                max="5"
                class="input-calif"
                :readonly="califIIBloqueada"
                :title="califIIBloqueada ? 'Traída de la API (promedio alumnos / 2)' : 'Ingresa manualmente'"
                :style="califIIBloqueada ? 'background-color:white;border:none;font-weight:bold;text-align:center;pointer-events:none;' : ''"
              />
            </div>
          </div>

          <div class="col-der">
            <div class="total-box">
              <!-- TOTAL EN ESCALA 0–10 (suma I + II) -->
              <span class="total">{{ total10.toFixed(1) }}</span>
            </div>
          </div>
        </div>
      </header>

      <!-- TABLA -->
      <div class="table-wrapper">
        <table class="tabla-evaluacion">
          <!-- fija anchos desde el HTML para que se vea bien desde el primer render -->
          <colgroup>
            <col style="width:22%">
            <col style="width:63%">
            <col style="width:15%">
          </colgroup>

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
                      <!-- I (0–5) -->
                      <td>{{ promedio.toFixed(1) }}</td>
                      <!-- II (0–5) -->
                      <td>{{ Number(califII || 0).toFixed(1) }}</td>
                    </tr>
                    <tr>
                      <td colspan="2" class="calif-final-titulo">Calificación (0–10):</td>
                    </tr>
                    <tr>
                      <td colspan="3" class="calif-final">
                        {{ total10.toFixed(1) }}
                      </td>
                    </tr>
                  </tbody>
                </table>
              </td>
            </tr>
          </tfoot>
        </table>
      </div>

      <!-- FIRMA2 -->
      <section class="firma">
        <strong>Elaborado por: </strong>
        <span class="linea-firma"></span>
        <div class="nombre-firma">{{ form.evaluador }}</div>
      </section>
    </main>
  </Menu>
</template>


<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue'
import axios from '@/lib/axios'
import Menu from '@/layouts/Menu.vue'
import html2canvas from 'html2canvas'
import jsPDF from 'jspdf'
import * as XLSX from 'xlsx'
import { saveAs } from 'file-saver'

const props = defineProps({ id: { type: Number, required: true } })

const showDownload = ref(false)
const califII = ref(0)           // (0–5)
const comentario = ref('')
const evaluado = ref(false)
const califIIBloqueada = ref(false)

/** Carga promedio alumnos (0–10) y lo convierte a (0–5) */
async function cargarCalifEstudiante() {
  try {
    const { data } = await axios.get(`/admin/reportes/puntaje-final/${props.id}`)
    if (data && data.promedio_alumnos != null) {
      const valor5 = Number(data.promedio_alumnos) / 2
      califII.value = Number((isNaN(valor5) ? 0 : valor5).toFixed(1))
      califIIBloqueada.value = true
    } else {
      califIIBloqueada.value = false
    }
  } catch (e) {
    console.error('Error cargando promedio de alumnos:', e)
    califIIBloqueada.value = false
  }
}

const form = reactive({
  nombre: '',
  puesto: '',
  evaluador: '',
  periodo: new Date().getFullYear().toString()
})

const camposFormulario = {
  nombre: 'Nombre',
  puesto: 'Puesto',
  evaluador: 'Evaluador',
  periodo: 'Periodo'
}

const preguntas = ref([])

const storageKey = computed(() => `evaluado:PTC:${props.id}:${form.periodo}`)

const obtenerPreguntas = async () => {
  try {
    const res = await axios.get('/evaluaciones/preguntas-ptc')
    preguntas.value = res.data.map(p => ({ ...p, calificacion: 0 }))
  } catch (error) {
    console.error('Error al obtener preguntas:', error)
  }
}

const obtenerEvaluador = async () => {
  try {
    const { data } = await axios.get('/admin/me')
    form.evaluador = data?.nombre || data?.correo || 'Admin'
  } catch {
    try {
      const admin = JSON.parse(localStorage.getItem('admin') || '{}')
      form.evaluador = admin?.nombre_completo || admin?.correo || 'No identificado'
    } catch {
      form.evaluador = 'No identificado'
    }
  }
}

const cargarDatosProfesor = async () => {
  try {
    const res = await axios.get(`/evaluaciones/profesor/${props.id}`)
    form.nombre = res.data.nombre_completo
    form.puesto = res.data.cargo
  } catch (error) {
    console.error('Error al cargar profesor:', error)
  }
}

// I (Resp. PE) en 0–5
const promedio = computed(() => {
  const total = preguntas.value.reduce((sum, p) => sum + Number(p.calificacion || 0), 0)
  return preguntas.value.length ? total / preguntas.value.length : 0
})

/** TOTAL en 0–10: suma de I (0–5) + II (0–5) */
const total10 = computed(() => {
  const i = Number(promedio.value) || 0
  const ii = Number(califII.value) || 0
  return Number((i + ii).toFixed(1))
})

function toggleDropdown() { showDownload.value = !showDownload.value }
function limitarCalificacion(p) {
  if (p.calificacion > 5) p.calificacion = 5
  else if (p.calificacion < 1) p.calificacion = 1
}

function downloadPDF() {
  const element = document.querySelector('.contenido-principal')
  html2canvas(element, { scale: 2 }).then(canvas => {
    const imgData = canvas.toDataURL('image/png')
    const pdf = new jsPDF('p', 'mm', 'a4')
    const imgProps = pdf.getImageProperties(imgData)
    const pdfWidth = pdf.internal.pageSize.getWidth()
    const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width
    pdf.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight)
    pdf.save(`evaluacion-profesor-${props.id}.pdf`)
  })
}

function downloadExcel() {
  const table = document.querySelector('.tabla-evaluacion')
  const wb = XLSX.utils.book_new()
  const ws = XLSX.utils.table_to_sheet(table)
  XLSX.utils.book_append_sheet(wb, ws, 'Evaluacion')
  XLSX.writeFile(wb, `evaluacion-profesor-${props.id}.xlsx`)
}

async function cargarEstadoEvaluadoBackend() {
  try {
    const { data } = await axios.get('/evaluaciones/estado', {
      params: { profesor_id: props.id, tipo: 'PTC', periodo: form.periodo }
    })
    evaluado.value = !!data.evaluado
    if (evaluado.value) localStorage.setItem(storageKey.value, '1')
    else evaluado.value = localStorage.getItem(storageKey.value) === '1'
  } catch {
    evaluado.value = localStorage.getItem(storageKey.value) === '1'
  }
}

const guardarEvaluacion = async () => {
  try {
    const payload = {
      profesor_id: props.id,
      tipo: 'PTC',
      periodo: form.periodo,
      calif_i: Number(promedio.value.toFixed(1)),           // 0–5
      calif_ii: Number((califII.value || 0).toFixed(1)),    // 0–5
      calificacion_final: Number(total10.value.toFixed(1)), // 0–10 (suma)
      comentario: comentario.value,
      respuestas: preguntas.value.map(p => ({
        pregunta_id: p.id,
        calificacion: Number(p.calificacion)
      }))
    }

    await axios.post('/evaluaciones', payload)
    evaluado.value = true
    localStorage.setItem(storageKey.value, '1')
    alert(' Evaluación enviada correctamente')
    window.close()
  } catch (error) {
    console.error(' Error al guardar evaluación:', error)
    alert('Ocurrió un error al guardar la evaluación')
  }
}

onMounted(() => {
  obtenerPreguntas()
  cargarDatosProfesor()
  obtenerEvaluador()
  cargarEstadoEvaluadoBackend()
  cargarCalifEstudiante() // trae promedio alumnos (0–10) y lo pone en 0–5
})

watch(() => props.id, () => {
  cargarDatosProfesor()
  cargarEstadoEvaluadoBackend()
  cargarCalifEstudiante()
})

watch(() => form.periodo, () => {
  cargarEstadoEvaluadoBackend()
  cargarCalifEstudiante()
})
</script>

<style src="@/../css/botones.css"></style>
<style src="@/../css/EvaluacionProfesores.css"></style>

<style>
.boton-gris {
  background-color: gray !important;
  color: #fff !important;
  cursor: not-allowed !important;
  opacity: 0.8;
  .evaluacion-page .tabla-evaluacion {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.95rem;
  table-layout: fixed;     /* respeta los widths del <colgroup> */
}
.evaluacion-page .tabla-evaluacion th,
.evaluacion-page .tabla-evaluacion td {
  word-break: break-word;
  overflow-wrap: anywhere;
}
.evaluacion-page .tabla-evaluacion td:nth-child(3) .input-calif,
.evaluacion-page .tabla-evaluacion td:nth-child(3) input[type="number"] {
  width: 80px;
  max-width: 100%;
}
}

</style>
