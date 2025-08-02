<template>
  <MenuAlumno>
    <main class="contenido-principal">
      <!-- Barra superior -->
      <div class="barra-superior">
        <div class="rectangulo">Profesor</div>
        <div class="rectangulo">Materia</div>
        <div class="rectangulo">Grupo</div>
      </div>

      <!-- FORMULARIOS -->
      <form @submit.prevent="handleSubmit">
        <div v-if="step === 1">
          <section v-for="(bloque, idx) in preguntasPorClasificacion" :key="idx">
            <h3>{{ idx + 1 }}.- {{ bloque.clasificacion }}</h3>

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

            <div v-if="bloque.tipo === 'comentario'" class="comentario-wrapper">
              <div v-for="preg in bloque.preguntas" :key="preg.id" class="comentario-item">
                <label>{{ preg.texto }}</label>
                <textarea v-model="comentariosRespuesta[preg.id]" rows="4"></textarea>
              </div>
            </div>
          </section>
        </div>
      </form>
    </main>
  </MenuAlumno>
</template>

<script setup>
import { ref } from 'vue'
import MenuAlumno from '@/layouts/MenuAlumno.vue'

const step = ref(1)

// Datos simulados para preguntas
const preguntasPorClasificacion = ref([
  {
    clasificacion: 'Satisfacción General',
    tipo_opciones: 'puntuacion',
    tipo: 'puntuacion',
    preguntas: [
      { id: 1, texto: '¿Cómo calificarías la calidad general del servicio?' },
      { id: 2, texto: '¿Recomendarías nuestro servicio a otros?' }
    ]
  },
  {
    clasificacion: 'Tiempo de Respuesta',
    tipo_opciones: 'fecha',
    tipo: 'fecha',
    preguntas: [
      { id: 3, texto: '¿Con qué frecuencia recibes respuestas dentro del plazo prometido?' }
    ]
  },
  {
    clasificacion: 'Comentarios',
    tipo_opciones: 'comentario',
    tipo: 'comentario',
    preguntas: [
      { id: 4, texto: '¿Qué sugerencias o comentarios tienes para mejorar el servicio?' }
    ]
  }
])

const respuestas = ref({})
const comentariosRespuesta = ref({})

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

function obtenerOpciones(tipo_opciones) {
  return opcionesPorTipo[ mapTipo(tipo_opciones) ] || []
}

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

function handleSubmit() {
  console.log('Respuestas:', respuestas.value)
  console.log('Comentarios:', comentariosRespuesta.value)
}
</script>

<style src="@/../css/Registros.css"></style>
<style src="@/../css/botones.css"></style>
<style src="@/../css/Evaluacion.css"></style>
