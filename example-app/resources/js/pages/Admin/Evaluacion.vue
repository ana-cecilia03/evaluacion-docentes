<template>
  <Menu>
    <main class="contenido-principal">
      <div class="barra-superior">
        <button type="submit" class="boton-azul">Publicar</button>
      </div>

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
    </main>
  </Menu>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import Menu from '@/layouts/Menu.vue'
import axios from 'axios'

const step = ref(1)
const preguntasPorClasificacion = ref([])
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

onMounted(async () => {
  const { data } = await axios.get('/api/preguntas-alumno')
  agruparPorClasificacion(data.preguntas)
})

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

function obtenerOpciones(tipo_opciones) {
  return opcionesPorTipo[ mapTipo(tipo_opciones) ] || []
}

</script>


