<template>
  <teleport to="body">
    <div class="modal-overlay" @click.self="cerrar">
      <div class="modal-content">
        <h2>Actualizar</h2>

        <!-- Clasificación -->
        <div class="form-group">
          <label>Clasificación:</label>
          <select v-model="clasificacionSeleccionada">
            <option value="">Seleccionar clasificación</option>
            <option v-for="c in Array.from(clasificacionesDisponibles)" :key="c" :value="c">{{ c }}</option>
            <option value="otro">Otro</option>
          </select>

          <div v-if="clasificacionSeleccionada === 'otro'" class="campo-extra">
            <label>Escribe otra clasificación:</label>
            <input type="text" v-model="otraClasificacion" />
          </div>
        </div>

        <!-- Pregunta -->
        <div class="form-group">
          <label>Pregunta:</label>
          <input type="text" v-model="form.texto" />
        </div>

        <!-- Respuesta -->
        <div class="form-group">
          <label>Respuesta:</label>
          <select v-model="form.tipo_opciones">
            <option value="">Seleccionar respuesta</option>
            <option>Excelente, Muy bien, Bien, Malo</option>
            <option>Excelente, Muy bien, Bien, Regular, Malo</option>
            <option>Siempre, De 1 a 3 veces por semana, De 1 a 3 veces por mes, De 1 a 3 veces por Cuatrimestre, Nunca</option>
            <option>Buena combinación de teoría y práctica, Demasiada teoría y poca práctica, Poca teoría y mucha práctica, Poca teoría y poca práctica</option>
            <option>Comentario</option>
          </select>
        </div>

        <!-- Botones -->
        <div class="button-group">
          <button class="editar-rojo" @click="cerrar">Cancelar</button>
          <button class="editar-verde" @click="actualizar">Actualizar</button>
        </div>
      </div>
    </div>
  </teleport>
</template>

<script setup>
import { reactive, ref, watch, onMounted } from 'vue'
import axios from 'axios'

const props = defineProps({ pregunta: Object })
const emit = defineEmits(['cerrar', 'actualizado'])

const form = reactive({
  clasificacion: '',
  texto: '',
  tipo_opciones: ''
})

const clasificacionSeleccionada = ref('')
const otraClasificacion = ref('')
const clasificacionesDisponibles = ref(new Set([
  'Evaluación del facilitador.',
  'Habilidades del facilitador.',
  'Utilización de los medios didácticos.',
  'Marca la respuesta que consideres adecuada.',
  'Autoevaluación de alumno.',
  'Comentarios'
]))

watch(() => props.pregunta, (nuevo) => {
  if (nuevo) {
    form.clasificacion = nuevo.clasificacion
    form.texto = nuevo.texto
    form.tipo_opciones = nuevo.tipo_opciones

    // Verificar si ya existe en el set, si no agregarla
    if (!clasificacionesDisponibles.value.has(nuevo.clasificacion)) {
      clasificacionesDisponibles.value.add(nuevo.clasificacion)
    }

    if (clasificacionesDisponibles.value.has(nuevo.clasificacion)) {
      clasificacionSeleccionada.value = nuevo.clasificacion
      otraClasificacion.value = ''
    } else {
      clasificacionSeleccionada.value = 'otro'
      otraClasificacion.value = nuevo.clasificacion
    }
  }
}, { immediate: true })

onMounted(async () => {
  const { data } = await axios.get('/api/preguntas-alumno')
  data.preguntas.forEach(p => {
    clasificacionesDisponibles.value.add(p.clasificacion)
  })
})

const cerrar = () => emit('cerrar')

const actualizar = async () => {
  form.clasificacion =
    clasificacionSeleccionada.value === 'otro'
      ? otraClasificacion.value.trim()
      : clasificacionSeleccionada.value

  // Actualizar la pregunta en la base de datos
  await axios.put(`/api/preguntas-alumno/${props.pregunta.id}`, {
    clasificacion: form.clasificacion,
    texto: form.texto,
    tipo_opciones: form.tipo_opciones
  })

  // Emitir evento de actualización
  emit('actualizado')

  // Cerrar el modal después de la actualización
  cerrar()
}
</script>

<style src="@css/global.css" lang="css"></style>
