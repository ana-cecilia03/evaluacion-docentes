<template>
  <Menu>
    <main class="contenido-principal evaluacion-page">
      <!-- CABECERA -->
      <header class="encabezado-evaluacion">
        <h1 class="titulo">Evaluación de Profesores PA</h1>

        <!-- Botones de acciones (Descargar + Evaluar) -->
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
          <!-- Columna izquierda: datos informativos -->
          <div class="col-izq">
            <div class="campo" v-for="(label, key) in camposFormulario" :key="key">
              <label>{{ label }}:</label>
              <input :value="form[key]" type="text" disabled />
            </div>
          </div>

          <!-- Columna central: Calificaciones I y II -->
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
              <!-- Si viene de API, queda bloqueado (promedio alumnos / 2) -->
              <input
                v-model.number="califII"
                type="number"
                step="0.1"
                min="0"
                max="5"
                class="input-calif"
                :readonly="califIIBloqueada"
                :title="califIIBloqueada ? 'Valor desde API (promedio alumnos / 2)' : 'Ingresa manualmente'"
                :style="califIIBloqueada ? 'background-color:white;border:none;font-weight:bold;font-size:1.2rem;text-align:center;pointer-events:none;' : ''"
              />
            </div>
          </div>

          <!-- Columna derecha: TOTAL 0–10 = I(0–5) + II(0–5) -->
          <div class="col-der">
            <div class="total-box">
              <span class="total">{{ total10.toFixed(1) }}</span>
            </div>
          </div>
        </div>
      </header>

      <!-- TABLA -->
      <div class="table-wrapper">
        <table class="tabla-evaluacion">
          <!-- Anchos fijos desde HTML para que se vea bien desde el primer render -->
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
/**
 * PA: I y II en 0–5; TOTAL 0–10 = I + II
 */
import { ref, reactive, computed, onMounted, watch } from 'vue'
import axios from '@/lib/axios'
import Menu from '@/layouts/Menu.vue'
import html2canvas from 'html2canvas'
import jsPDF from 'jspdf'
import * as XLSX from 'xlsx'
import { saveAs } from 'file-saver'

const props = defineProps({ id: { type: Number, required: true } })

// Estado UI
const showDownload = ref(false)
const califII = ref(0)          // 0–5
const comentario = ref('')
const evaluado = ref(false)
const califIIBloqueada = ref(false)

// Encabezado
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

// Preguntas (1–5)
const preguntas = ref([])

const storageKey = computed(() => `evaluado:PA:${props.id}:${form.periodo}`)

const obtenerPreguntas = async () => {
  try {
    const res = await axios.get('/evaluaciones/preguntas-pa')
    preguntas.value = res.data.map(p => ({ ...p, calificacion: 0 }))
  } catch (error) {
    console.error('Error al obtener preguntas:', error)
  }
}

const obtenerEvaluador = async () => {
  try {
    const { data } = await axios.get('/admin/me')
    form.evaluador = data?.nombre || data?.correo || 'Admin'
  } catch (error) {
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

// Promedio I (0–5)
const promedio = computed(() => {
  const total = preguntas.value.reduce((sum, p) => sum + Number(p.calificacion || 0), 0)
  return preguntas.value.length ? total / preguntas.value.length : 0
})

/** Total 0–10 = I(0–5) + II(0–5) */
const total10 = computed(() => {
  const i = Number(promedio.value) || 0
  const ii = Number(califII.value) || 0
  return Number((i + ii).toFixed(1))
})

// Dropdown
function toggleDropdown() { showDownload.value = !showDownload.value }

// Validación inputs 1–5
function limitarCalificacion(pregunta) {
  if (pregunta.calificacion > 5) pregunta.calificacion = 5
  else if (pregunta.calificacion < 1) pregunta.calificacion = 1
}

/** Trae promedio alumnos (0–10) y lo convierte a 0–5 */
async function cargarCalifEstudiante() {
  try {
    const { data } = await axios.get(`/admin/reportes/puntaje-final/${props.id}`)
    if (data && data.promedio_alumnos !== null && data.promedio_alumnos !== undefined) {
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

// PDF
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

// Excel
function downloadExcel() {
  const table = document.querySelector('.tabla-evaluacion')
  const wb = XLSX.utils.book_new()
  const ws = XLSX.utils.table_to_sheet(table)
  XLSX.utils.book_append_sheet(wb, ws, 'Evaluacion')
  XLSX.writeFile(wb, `evaluacion-profesor-${props.id}.xlsx`)
}

// Estado evaluado (combina backend + local)
async function cargarEstadoEvaluadoBackend() {
  try {
    const { data } = await axios.get('/evaluaciones/estado', {
      params: { profesor_id: props.id, tipo: 'PA', periodo: form.periodo }
    })
    evaluado.value = !!data.evaluado
    if (evaluado.value) localStorage.setItem(storageKey.value, '1')
    else evaluado.value = localStorage.getItem(storageKey.value) === '1'
  } catch {
    evaluado.value = localStorage.getItem(storageKey.value) === '1'
  }
}

// Guardar (envía calif_i 0–5, calif_ii 0–5, calificacion_final 0–10)
const guardarEvaluacion = async () => {
  try {
    const payload = {
      profesor_id: props.id,
      tipo: 'PA',
      periodo: form.periodo,
      calif_i: Number(promedio.value.toFixed(1)),            // 0–5
      calif_ii: Number((califII.value || 0).toFixed(1)),     // 0–5
      calificacion_final: Number(total10.value.toFixed(1)),  // 0–10 (suma)
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
    console.error('Error al guardar evaluación:', error)
    alert('Ocurrió un error al guardar la evaluación')
  }
}

// Montaje
onMounted(() => {
  obtenerPreguntas()
  cargarDatosProfesor()
  obtenerEvaluador()
  cargarEstadoEvaluadoBackend()
  cargarCalifEstudiante()
})

// Si cambia profe o periodo, recarga estados y promedio alumnos
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

<style src="@/../css/global.css"></style>

<style>
.boton-gris {
  background-color: gray !important;
  color: #fff !important;
  cursor: not-allowed !important;
  opacity: 0.8;
}
</style>
