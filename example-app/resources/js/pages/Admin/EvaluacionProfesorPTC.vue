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
              <span class="total">{{ total10.toFixed(1) }}</span>
            </div>
          </div>
        </div>
      </header>

      <!-- TABLA -->
      <div class="table-wrapper">
        <table class="tabla-evaluacion">
          <colgroup>
            <col style="width:20%">
            <col style="width:50%">
            <col style="width:20%">
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
              <!-- control de alto con CSS (line-clamp) -->
              <td class="celda-definicion">{{ pregunta.definicion }}</td>
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
<style scoped>
/* ====== Ajustes locales para NO romper tus globales ====== */
.evaluacion-page { max-width: 1200px; margin: 0 auto; }

/* Encabezado / acciones */
.encabezado-evaluacion { overflow: visible; }
.titulo { text-align: center; margin-bottom: 1rem; }
.acciones { display: flex; justify-content: flex-end; margin-bottom: 1rem; gap: .5rem; flex-wrap: wrap; }
.download-wrapper { position: relative; z-index: 2000; }

.boton-verde { background: #0a7a32; color: #fff; border: 0; border-radius: 20px; padding: .5rem 1.2rem; font-weight: 600; cursor: pointer; }
.boton-gris  { background: #9ca3af; color: #fff; border: 0; border-radius: 20px; padding: .5rem 1.2rem; font-weight: 600; cursor: not-allowed; opacity: .9; }

.dropdown {
  position: absolute; right: 0; top: calc(100% + 6px);
  background: #fff; box-shadow: 0 4px 14px rgba(0,0,0,.15);
  border-radius: 8px; list-style: none; padding: .25rem 0; min-width: 140px;
}
.dropdown li { padding: .6rem 1rem; cursor: pointer; }
.dropdown li:hover { background: #f2f2f2; }

/* Grid superior */
.datos-grid { display: grid; grid-template-columns: 1fr auto 160px; gap: 1rem; align-items: flex-start; }
.col-izq .campo { display: grid; grid-template-columns: 120px 1fr; gap: .5rem; margin-bottom: .4rem; align-items: center; }
.col-izq input { width: 100%; background: #f7f7f7; border: 1px solid #d7d7d7; border-radius: 8px; padding: .35rem .6rem; }
.col-mid { display: flex; flex-direction: column; gap: .6rem; }
.box-calif {
  background: #f7f7f7; border: 1px solid #d7d7d7; border-radius: 8px;
  padding: .5rem .75rem; display: flex; flex-direction: column; align-items: center;
}
.titulo-box { text-align: center; font-weight: 600; margin-bottom: .3rem; }
.col-der .total-box {
  min-height: 140px; background: #f7f7f7; border: 1px solid #d7d7d7; border-radius: 8px;
  display: flex; align-items: center; justify-content: center;
}
.total-box .total { font-size: 3rem; font-weight: 600; }

/* ====== TABLA ====== */
.table-wrapper {
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 10px;
  overflow: auto;
  max-height: 62vh;
}

.tabla-evaluacion {
  width: 100%;
  border-collapse: collapse;
  table-layout: fixed;   /* respeta colgroup */
  font-size: .95rem;
}

.tabla-evaluacion thead th {
  position: sticky; top: 0; z-index: 1;
  background: #343a40; font-weight: 700;
  box-shadow: inset 0 -1px 0 #e5e7eb;
}

.tabla-evaluacion th,
.tabla-evaluacion td {
  border: 1px solid #ccc;
  padding: .75rem;
  vertical-align: top;
  word-break: break-word;
  overflow-wrap: anywhere;
  white-space: normal;
  line-height: 1.5;
}

/* Texto de Definición fluye como párrafo */
.celda-definicion { white-space: normal; }

/* Inputs de calificación compactos */
.input-calif,
.tabla-evaluacion td:nth-child(3) input[type="number"] {
  width: 80px; max-width: 100%;
  text-align: center; font-weight: 600;
  border: 1px solid #ccc; border-radius: 6px; padding: .3rem;
}

/* ====== Footer (mini tabla súper compacta) ====== */
.comentario-wrapper { padding: 0; }
.comentario {
  width: 100%; height: 110px; padding: .8rem;
  border: none; outline: none; resize: vertical; color: #444;
}

/* Celda contenedora: bloque normal, alineada a la derecha */
.resumen-wrapper {
  padding: 0;
  vertical-align: middle;
  display: block;
  text-align: right;
  min-width: 100px;
  max-width: 140px;
}

/* mini-tabla: no se estira; solo lo necesario */
.tabla-resumen{
  width: auto;
  min-width: 100px;
  max-width: 130px;
  margin: 0;
  border-collapse: collapse;
  text-align: center;
  background: #f7f7f7;
  border-radius: 6px;
  overflow: hidden;
}

.tabla-resumen td{
  border: 1px solid #ccc;
  padding: .22rem .28rem;
  font-size: .82rem;
  font-weight: 600;
  line-height: 1.15;
  white-space: nowrap;   /* no se parten los números */
}

/* primera celda “Sub. total” bien angosta */
.sub-total-titulo{
  width: 50px;          /* ajusta 46–60px */
  white-space: nowrap;
  font-weight: 700;
}

.calif-final-titulo{ background: #eee; font-size: .8rem; padding: .2rem .28rem; }
.calif-final{ background: #e5e5e5; font-size: .9rem; font-weight: 700; padding: .28rem .3rem; }

/* Firma */
.firma { margin-top: 2rem; text-align: left; }
.linea-firma {
  display: inline-block; width: calc(100% - 130px); height: 2px; background: #000;
  margin-left: .5rem; vertical-align: middle;
}
.nombre-firma { text-align: center; }

/* Responsive */
@media (max-width: 1024px) {
  .datos-grid { grid-template-columns: 1fr 1fr; }
  .col-der { grid-column: 1 / -1; }
  .table-wrapper { max-height: 58vh; }
}
@media (max-width: 768px) {
  .datos-grid { grid-template-columns: 1fr; }
  .total-box .total { font-size: 2.4rem; }
  .tabla-evaluacion { font-size: .87rem; }
}
</style>






