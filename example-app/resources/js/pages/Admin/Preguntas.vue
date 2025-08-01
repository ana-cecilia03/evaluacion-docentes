<template>
  <Menu>
    <main class="contenido-principal">
      <div class="relacion-filtros">
        <div class="relacion-columna">
          <label>Clasificación:</label>
          <select v-model="clasificacionSeleccionada">
            <option value="">Seleccionar clasificación</option>
            <option>Evaluación del facilitador.</option>
            <option>Habilidades del facilitador.</option>
            <option>Utilización de lo medios didácticos.</option>
            <option>Marca la respuesta que consideres adecuada.</option>
            <option>Autoevaluación de alumno.</option>
            <option>Comentarios</option>
            <option value="otro">Otro</option>
          </select>
          <div v-if="clasificacionSeleccionada === 'otro'" class="campo-extra">
            <label>Escribe otra clasificación:</label>
            <input type="text" v-model="otraClasificacion" />
          </div>
        </div>

        <div class="relacion-columna">
          <label>Pregunta:</label>
          <input type="text" v-model="preguntaTexto" />
        </div>

        <div class="relacion-columna">
          <label>Respuesta:</label>
          <select v-model="tipoOpciones">
            <option value="">Seleccionar respuesta</option>
            <option>Excelente, Muy bien, Bien, Malo</option>
            <option>Excelente, Muy bien, Bien, Regular, Malo</option>
            <option>Siempre, De 1 a 3 veces por semana, De 1 a 3 veces por mes, De 1 a 3 veces por Cuatrimestre, Nunca</option>
            <option>Buena combinacion de teoria y practica, Demasiada teoria y poca practica, Poca teoria y mucha practica, Poca teoria y poca practica</option>
            <option>Comentario</option>
          </select>
        </div>

        <div class="relacion-columna">
          <button class="boton-azul" @click="agregarPregunta">Agregar</button>
        </div>
      </div>

      <div class="modal-overlay" v-if="editarDatos">
        <div class="modal-content">
          <EditarPreguntas :pregunta="preguntaSeleccionada" @cerrar="editarDatos = false" @actualizado="cargarPreguntas" />
        </div>
      </div>

      <div class="table-wrapper">
        <table>
          <thead>
            <tr>
              <th>Clasificación</th>
              <th>Pregunta</th>
              <th>Respuesta</th>
              <th>Acción</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="pregunta in preguntas" :key="pregunta.id">
              <td>{{ pregunta.clasificacion }}</td>
              <td>{{ pregunta.texto }}</td>
              <td>{{ pregunta.tipo_opciones }}</td>
              <td>
                <button class="boton-verde" @click="editarPregunta(pregunta)">Editar</button>
              </td>
              <td>
                <button class="boton-rojo" @click="eliminarPregunta(pregunta.id)">Eliminar</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </main>
  </Menu>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import Menu from '@/layouts/Menu.vue'
import EditarPreguntas from '@/components/EditarPreguntas.vue'

const preguntas = ref([])
const clasificacionSeleccionada = ref('')
const otraClasificacion = ref('')
const preguntaTexto = ref('')
const tipoOpciones = ref('')
const editarDatos = ref(false)
const preguntaSeleccionada = ref({})

const obtenerClasificacionFinal = () =>
  clasificacionSeleccionada.value === 'otro' ? otraClasificacion.value : clasificacionSeleccionada.value

const cargarPreguntas = async () => {
  const { data } = await axios.get('/api/preguntas-alumno')
  preguntas.value = data.preguntas
}

const agregarPregunta = async () => {
  if (!preguntaTexto.value || !tipoOpciones.value || !obtenerClasificacionFinal()) return

  await axios.post('/api/preguntas-alumno', {
    texto: preguntaTexto.value,
    tipo_opciones: tipoOpciones.value,
    clasificacion: obtenerClasificacionFinal()
  })

  preguntaTexto.value = ''
  tipoOpciones.value = ''
  clasificacionSeleccionada.value = ''
  otraClasificacion.value = ''

  cargarPreguntas()
}

const editarPregunta = (pregunta) => {
  preguntaSeleccionada.value = { ...pregunta }
  editarDatos.value = true
}

const eliminarPregunta = async (id) => {
  await axios.delete(`/api/preguntas-alumno/${id}`)
  cargarPreguntas()
}

onMounted(() => cargarPreguntas())
</script>

<style src="@/../css/Registros.css"></style>
<style src="@/../css/relacion.css"></style>
<style src="@/../css/botones.css"></style>
