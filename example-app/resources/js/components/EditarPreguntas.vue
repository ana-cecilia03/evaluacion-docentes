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
            <option>Buena combinacion de teoria y practica, Demasiada teoria y poca practica, Poca teoria y mucha practica, Poca teoria y poca practica</option>
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
import { reactive, ref, watch } from 'vue'
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

watch(() => props.pregunta, (nuevo) => {
  if (nuevo) {
    form.clasificacion = nuevo.clasificacion
    form.texto = nuevo.texto
    form.tipo_opciones = nuevo.tipo_opciones

    // Determinar si la clasificación es personalizada
    if (
      [
        'Evaluación del facilitador.',
        'Habilidades del facilitador.',
        'Utilización de lo medios didácticos.',
        'Marca la respuesta que consideres adecuada.',
        'Autoevaluación de alumno.',
        'Comentarios'
      ].includes(nuevo.clasificacion)
    ) {
      clasificacionSeleccionada.value = nuevo.clasificacion
      otraClasificacion.value = ''
    } else {
      clasificacionSeleccionada.value = 'otro'
      otraClasificacion.value = nuevo.clasificacion
    }
  }
}, { immediate: true })

const cerrar = () => emit('cerrar')

const actualizar = async () => {
  form.clasificacion =
    clasificacionSeleccionada.value === 'otro' ? otraClasificacion.value : clasificacionSeleccionada.value

  await axios.put(`/api/preguntas-alumno/${props.pregunta.id}`, {
    clasificacion: form.clasificacion,
    texto: form.texto,
    tipo_opciones: form.tipo_opciones
  })

  alert('Pregunta actualizada')
  emit('actualizado')
}
</script>

<style src="@/../css/Editar.css"></style>
