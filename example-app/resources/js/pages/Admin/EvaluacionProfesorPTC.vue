<template>
  <Menu>
    <main class="contenido-principal evaluacion-page">
      <!-- CABECERA -->
      <header class="encabezado-evaluacion">
        <h1 class="titulo">Evaluación de Profesores PTC</h1>

        <!-- Botones de acciones (Descargar + Evaluar) con separación -->
        <div class="acciones acciones-botones">
          <!-- Botón Descargar con su dropdown -->
          <div class="download-wrapper">
            <button class="boton-verde" @click="toggleDropdown">Descargar</button>
            <ul v-if="showDownload" class="dropdown">
              <li @click="downloadPDF">PDF</li>
              <li @click="downloadExcel">Excel</li>
            </ul>
          </div>

          <!-- Botón Evaluar / Evaluado -->
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
                      <td colspan="3" class="calif-final">
                        {{ calificacionFinal.toFixed(1) }}
                      </td>
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
import { ref, reactive, computed, onMounted, watch } from 'vue'
import axios from '@/lib/axios'
import Menu from '@/layouts/Menu.vue'
import html2canvas from 'html2canvas'
import jsPDF from 'jspdf'
import * as XLSX from 'xlsx'
import { saveAs } from 'file-saver'

const props = defineProps({
  id: { type: Number, required: true }
})

const showDownload = ref(false)
const califII = ref(0)
const comentario = ref('')
const evaluado = ref(false)

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
    preguntas.value = res.data.map(p => ({
      ...p,
      calificacion: 0
    }))
  } catch (error) {
    console.error('Error al obtener preguntas:', error)
  }
}

const obtenerEvaluador = async () => {
  try {
    const { data } = await axios.get('/admin/me')
    form.evaluador = data?.nombre || data?.correo || 'Admin'
  } catch (error) {
    console.error('Error al obtener evaluador (me):', error)
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

const promedio = computed(() => {
  const total = preguntas.value.reduce((sum, p) => sum + Number(p.calificacion || 0), 0)
  return preguntas.value.length ? total / preguntas.value.length : 0
})

const calificacionFinal = computed(() => {
  const i = Number(promedio.value) || 0
  const ii = Number(califII.value) || 0
  return (i + ii) / 2
})

function toggleDropdown() {
  showDownload.value = !showDownload.value
}
function limitarCalificacion(pregunta) {
  if (pregunta.calificacion > 5) pregunta.calificacion = 5
  else if (pregunta.calificacion < 1) pregunta.calificacion = 1
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
    if (evaluado.value) {
      localStorage.setItem(storageKey.value, '1')
    } else {
      const local = localStorage.getItem(storageKey.value) === '1'
      evaluado.value = local
    }
  } catch (e) {
    const local = localStorage.getItem(storageKey.value) === '1'
    evaluado.value = local
  }
}

const guardarEvaluacion = async () => {
  try {
    const payload = {
      profesor_id: props.id,
      tipo: 'PTC',
      periodo: form.periodo,
      calif_i: Number(promedio.value.toFixed(1)),
      calif_ii: Number(califII.value || 0),
      calificacion_final: Number(calificacionFinal.value.toFixed(1)),
      comentario: comentario.value,
      respuestas: preguntas.value.map(p => ({
        pregunta_id: p.id,
        calificacion: Number(p.calificacion)
      }))
    }

    await axios.post('/evaluaciones', payload)
    evaluado.value = true
    localStorage.setItem(storageKey.value, '1')
    alert('✅ Evaluación enviada correctamente')

    window.close()
  } catch (error) {
    console.error('❌ Error al guardar evaluación:', error)
    alert('Ocurrió un error al guardar la evaluación')
  }
}

onMounted(() => {
  obtenerPreguntas()
  cargarDatosProfesor()
  obtenerEvaluador()
  cargarEstadoEvaluadoBackend()
})

watch(() => form.periodo, () => {
  cargarEstadoEvaluadoBackend()
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
}
</style>
