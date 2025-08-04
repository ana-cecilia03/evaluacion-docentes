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
                <th v-for="opcion in obtenerOpciones(bloque.tipo_opciones)" :key="opcion">{{ opcion }}</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(preg, i) in bloque.preguntas" :key="preg.id">
                <td v-if="bloque.tipo !== 'curso'">{{ i + 1 }}</td>
                <td>{{ preg.texto }}</td>
                <!-- Opciones tipo radio para seleccionar la respuesta -->
                <td v-for="opcion in obtenerOpciones(bloque.tipo_opciones)" :key="opcion">
                  <label class="opcion-responsive">
                    <input
                      type="radio"
                      :name="`preg-${preg.id}`"
                      :value="opcion"
                      v-model="respuestas[preg.id]"
                    />
                    <span class="etiqueta-opcion">{{ opcion }}</span>
                  </label>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Área de comentarios abiertos -->
        <div v-if="bloque.tipo === 'comentario'" class="comentario-wrapper">
          <div v-for="preg in bloque.preguntas" :key="preg.id" class="comentario-item">
            <label>{{ preg.texto }}</label>
            <textarea v-model="comentariosRespuesta[preg.id]" rows="4"></textarea>
          </div>
        </div>
      </section>

      <!-- Botón para enviar la evaluación -->
      <div class="boton-container">
        <button class="boton-azul" @click="enviarEvaluacion">Enviar</button>
      </div>
    </main>
  </MenuAlumno>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import MenuAlumno from '@/layouts/MenuAlumno.vue'
import axios from 'axios'

// Obtener el ID de relación desde props (pasado por Inertia desde la ruta)
const page = usePage()
const idRelacion = page.props.id_relacion

// Estados reactivos
const datosRelacion = ref({})
const preguntasPorClasificacion = ref([])
const respuestas = ref({})
const comentariosRespuesta = ref({})

// Diccionario de tipos de opciones posibles
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

// Al montar el componente, cargar datos de relación y preguntas
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

// Agrupa preguntas por su clasificación (tema o bloque)
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

// Mapea el tipo de pregunta para identificar la lógica de opciones
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

// Devuelve las opciones que se deben mostrar para un tipo de pregunta
function obtenerOpciones(tipo_opciones) {
  return opcionesPorTipo[mapTipo(tipo_opciones)] || []
}

// Enviar respuestas de evaluación al backend
async function enviarEvaluacion() {
  const payload = {
    relacion_id: idRelacion,
    respuestas: []
  }

  // Agregar respuestas seleccionadas
  for (const id in respuestas.value) {
    payload.respuestas.push({
      id_pregunta: parseInt(id),
      calificacion: respuestas.value[id]
    })
  }

  // Agregar comentarios
  for (const id in comentariosRespuesta.value) {
    payload.respuestas.push({
      id_pregunta: parseInt(id),
      calificacion: comentariosRespuesta.value[id]
    })
  }

  try {
    await axios.post('/api/alumno/evaluar', payload)
    alert('✅ Evaluación enviada correctamente.')
    router.visit('/alumno/materias')
  } catch (error) {
    console.error('Error al enviar evaluación:', error)
    alert('❌ Error al enviar evaluación.')
  }
}
</script>

<!-- Estilos CSS importados para esta vista -->
<style src="@/../css/Registros.css"></style>
<style src="@/../css/botones.css"></style>
<style src="@/../css/Evaluacion.css"></style>
