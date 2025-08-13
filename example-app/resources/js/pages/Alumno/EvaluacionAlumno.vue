<template>
  <!-- Layout principal del alumno -->
  <MenuAlumno>
    <main class="contenido-principal">
      <!-- Sección superior con datos informativos -->
      <div class="barra-superior">
        <div class="rectangulo">Profesor: {{ datosRelacion.profesor }}</div>
        <div class="rectangulo">Materia: {{ datosRelacion.materia }}</div>
        <div class="rectangulo">Grupo: {{ datosRelacion.grupo }}</div>
        <div class="rectangulo">Fecha: {{ datosRelacion.fecha }}</div>
      </div>

      <!-- Aviso de preguntas faltantes -->
      <div v-if="faltantesListado.length" class="alerta-error" role="alert">
        <strong>Te faltan {{ faltantesListado.length }} preguntas por contestar:</strong>
        <ul>
          <li v-for="f in faltantesListado" :key="f.id">
            • {{ f.texto }} <span class="etiqueta-clasificacion">({{ f.clasificacion }})</span>
          </li>
        </ul>
      </div>

      <!-- Sección principal: agrupación de preguntas por clasificación -->
      <section v-for="(bloque, idx) in preguntasPorClasificacion" :key="idx">
        <h3>{{ idx + 1 }}.- {{ bloque.clasificacion }}</h3>

        <!-- Tabla de preguntas con opciones de evaluación (excepto comentarios) -->
        <div v-if="bloque.tipo !== 'comentario'" class="table-wrapper">
          <table>
            <thead>
              <tr>
                <th v-if="bloque.tipo !== 'curso'">No.</th>
                <th>Concepto</th>
                <th v-for="opcion in obtenerOpciones(bloque.tipo_opciones)" :key="opcion">
                  {{ opcion }}
                </th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="(preg, i) in bloque.preguntas"
                :key="preg.id"
                :ref="el => setPreguntaRef(preg.id, el)"
                :class="{'fila-error': errores[preg.id]}"
              >
                <td v-if="bloque.tipo !== 'curso'">{{ i + 1 }}</td>
                <td>
                  {{ preg.texto }}
                  <span v-if="errores[preg.id]" class="msg-error">Requerida</span>
                </td>
                <td v-for="opcion in obtenerOpciones(bloque.tipo_opciones)" :key="opcion">
                  <label class="opcion-responsive">
                    <input
                      type="radio"
                      :name="`preg-${preg.id}`"
                      :value="opcion"
                      v-model="respuestas[preg.id]"
                      @change="limpiarError(preg.id)"
                    />
                    <span class="etiqueta-opcion">{{ opcion }}</span>
                  </label>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Área de comentarios abiertos -->
        <div v-else class="comentario-wrapper">
          <div v-for="preg in bloque.preguntas" :key="preg.id" class="comentario-item">
            <label>{{ preg.texto }}</label>
            <textarea v-model="comentariosRespuesta[preg.id]" rows="4"></textarea>
          </div>
        </div>
      </section>

      <!-- Botón para enviar la evaluación -->
      <div class="boton-container">
        <button
          class="boton-azul"
          @click="enviarEvaluacion"
          :disabled="enviando"
        >
          {{ enviando ? 'Enviando…' : 'Enviar' }}
        </button>
      </div>
    </main>
  </MenuAlumno>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import MenuAlumno from '@/layouts/MenuAlumno.vue'
import axios from 'axios'

const page = usePage()
const idRelacion = page.props.id_relacion

// Estados
const datosRelacion = ref({})
const preguntasPorClasificacion = ref([])
const respuestas = ref({})              // { [idPregunta]: 'Excelente' | ... }
const comentariosRespuesta = ref({})    // { [idPregunta]: 'texto' }
const errores = ref({})                 // { [idPregunta]: true }
const faltantesListado = ref([])        // [{id, texto, clasificacion}]
const enviando = ref(false)

// Para hacer scroll a la primera pregunta con error
const preguntaRefs = new Map()
function setPreguntaRef(id, el) {
  if (el) preguntaRefs.set(id, el)
}
function scrollToPregunta(id) {
  const el = preguntaRefs.get(id)
  if (el && typeof el.scrollIntoView === 'function') {
    el.scrollIntoView({ behavior: 'smooth', block: 'center' })
  }
}

// Diccionario de opciones visuales por tipo de pregunta
const opcionesPorTipo = {
  puntuacion: ['Excelente', 'Muy bien', 'Bien', 'Malo'],
  fecha: [
    'Siempre',
    'De 1 a 3 veces por semana',
    'De 1 a 3 veces por mes',
    'De 1 a 3 veces por Cuatrimestre',
    'Nunca'
  ],
  curso: [
    'Buena combinacion de teoria y practica',
    'Demasiada teoria y poca practica',
    'Poca teoria y mucha practica',
    'Poca teoria y poca practica'
  ],
  detalle: ['Excelente', 'Muy bien', 'Bien', 'Regular', 'Malo']
}

const valoresNumericos = {
  'Excelente': 10,
  'Muy bien': 8,
  'Bien': 6,
  'Regular': 5,
  'Malo': 3,
  'Siempre': 10,
  'De 1 a 3 veces por semana': 8,
  'De 1 a 3 veces por mes': 6,
  'De 1 a 3 veces por Cuatrimestre': 4,
  'Nunca': 0,
  'Buena combinacion de teoria y practica': 10,
  'Demasiada teoria y poca practica': 7,
  'Poca teoria y mucha practica': 7,
  'Poca teoria y poca practica': 3
}

// Cargar datos
onMounted(async () => {
  try {
    const resRelacion = await axios.get(`/api/alumno/evaluacion-datos/${idRelacion}`)
    datosRelacion.value = resRelacion.data

    const resPreguntas = await axios.get('/api/alumno/preguntas')
    agruparPorClasificacion(resPreguntas.data.preguntas)
  } catch (error) {
    console.error('Error al cargar datos de evaluación:', error)
  }
})

// Agrupar preguntas por clasificación
function agruparPorClasificacion(preguntas) {
  const aux = {}
  preguntas.forEach(p => {
    const tipo = mapTipo(p.tipo_opciones)
    if (!aux[p.clasificacion]) {
      aux[p.clasificacion] = {
        clasificacion: p.clasificacion,
        tipo_opciones: p.tipo_opciones,
        tipo,
        preguntas: []
      }
    }
    aux[p.clasificacion].preguntas.push(p)
  })
  preguntasPorClasificacion.value = Object.values(aux)
}

// Detectar tipo lógico
function mapTipo(tipo_opciones) {
  if (!tipo_opciones) return 'puntuacion'
  const opt = tipo_opciones.toLowerCase().split(',').map(s => s.trim())
  if (tipo_opciones === 'comentario' || opt.includes('comentario')) return 'comentario'
  if (opt.includes('buena combinacion de teoria y practica')) return 'curso'
  if (opt.includes('siempre') && opt.some(s => s.includes('por semana'))) return 'fecha'
  if (opt.includes('regular') && opt.includes('muy bien')) return 'detalle'
  if (opt.includes('muy bien')) return 'puntuacion'
  return 'puntuacion'
}

// Opciones según tipo
function obtenerOpciones(tipo_opciones) {
  return opcionesPorTipo[mapTipo(tipo_opciones)] || []
}

// Limpia el error visual de una pregunta al responder
function limpiarError(idPregunta) {
  if (errores.value[idPregunta]) {
    const copia = { ...errores.value }
    delete copia[idPregunta]
    errores.value = copia
    faltantesListado.value = faltantesListado.value.filter(f => f.id !== idPregunta)
  }
}

// Construye el listado de faltantes (solo preguntas no-comentario)
function calcularFaltantes() {
  const faltantes = []
  const erroresTmp = {}

  preguntasPorClasificacion.value.forEach(bloque => {
    if (bloque.tipo === 'comentario') return
    bloque.preguntas.forEach(p => {
      const respondida = respuestas.value[p.id] !== undefined && respuestas.value[p.id] !== ''
      if (!respondida) {
        erroresTmp[p.id] = true
        faltantes.push({
          id: p.id,
          texto: p.texto,
          clasificacion: bloque.clasificacion
        })
      }
    })
  })

  errores.value = erroresTmp
  faltantesListado.value = faltantes
  return faltantes
}

// Enviar respuestas
async function enviarEvaluacion() {
  const faltantes = calcularFaltantes()
  if (faltantes.length > 0) {
    await nextTick()
    scrollToPregunta(faltantes[0].id)
    return
  }

  const alumno = JSON.parse(localStorage.getItem('alumno'))

  const payload = {
    id_alumno: alumno.id_alumno, // 👈 Agregado
    relacion_id: idRelacion,
    respuestas: [],
    comentarios: []
  }

  Object.keys(respuestas.value).forEach(id => {
    payload.respuestas.push({
      id_pregunta: parseInt(id),
      calificacion: valoresNumericos[respuestas.value[id]] ?? 0
    })
  })

  Object.keys(comentariosRespuesta.value).forEach(id => {
    payload.comentarios.push({
      id_pregunta: parseInt(id),
      texto: comentariosRespuesta.value[id]
    })
  })

  try {
    enviando.value = true
    await axios.post('/api/alumno/evaluar', payload)
    alert('Evaluación enviada correctamente.')
    router.visit('/alumno/materias')
  } catch (error) {
    console.error('Error al enviar evaluación:', error)
    alert('Error al enviar evaluación.')
  } finally {
    enviando.value = false
  }
}

</script>

<!-- Estilos adicionales mínimos para la validación visual -->
<style>
.alerta-error {
  background: #fdecea;
  color: #611a15;
  border: 1px solid #f5c6cb;
  padding: 12px;
  border-radius: 6px;
  margin-bottom: 16px;
  font-size: 0.95rem;
}
.alerta-error ul {
  margin: 8px 0 0 18px;
}
.etiqueta-clasificacion {
  font-style: italic;
  color: #8a3b36;
}
.fila-error {
  background: #fff6f6;
}
.msg-error {
  margin-left: 8px;
  font-size: 0.85rem;
  color: #c0392b;
  font-weight: 600;
}
</style>

<!-- Estilos externos específicos de esta vista -->

