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
        <!-- 1. Evaluación del Facilitador -->
        <div v-if="step === 1">
          <h3>1.- Evaluación del Facilitador</h3>
          <table>
            <thead>
              <tr>
                <th>No.</th>
                <th>Factor</th>
                <th>Excelente</th>
                <th>Muy bueno</th>
                <th>Bueno</th>
                <th>Nunca</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in facilitador" :key="index">
                <td>{{ item.no }}</td>
                <td>{{ item.texto }}</td>
                <td><input type="radio" :name="'fac-'+index" value="Excelente" v-model="respuestas1[index]" /></td>
                <td><input type="radio" :name="'fac-'+index" value="Muy bueno" v-model="respuestas1[index]" /></td>
                <td><input type="radio" :name="'fac-'+index" value="Bueno" v-model="respuestas1[index]" /></td>
                <td><input type="radio" :name="'fac-'+index" value="Nunca" v-model="respuestas1[index]" /></td>
              </tr>
            </tbody>
          </table>

          <h3>2.- Habilidades del Facilitador</h3>
          <table>
            <thead>
              <tr>
                <th>No.</th>
                <th>Concepto</th>
                <th>Excelente</th>
                <th>Muy bueno</th>
                <th>Bueno</th>
                <th>Nunca</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in habilidades" :key="index">
                <td>{{ item.no }}</td>
                <td>{{ item.texto }}</td>
                <td><input type="radio" :name="'hab-'+index" value="Excelente" v-model="respuestas2[index]" /></td>
                <td><input type="radio" :name="'hab-'+index" value="Muy bueno" v-model="respuestas2[index]" /></td>
                <td><input type="radio" :name="'hab-'+index" value="Bueno" v-model="respuestas2[index]" /></td>
                <td><input type="radio" :name="'hab-'+index" value="Nunca" v-model="respuestas2[index]" /></td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- 2. Utilización de Medios Didácticos -->
        <div v-if="step === 2">
          <h3>3.- Utilización de los Medios Didácticos</h3>
          <table>
            <thead>
              <tr>
                <th>No.</th>
                <th>Concepto</th>
                <th>Siempre</th>
                <th>Semana</th>
                <th>Mes</th>
                <th>Cuatrimestre</th>
                <th>Nunca</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in medios" :key="index">
                <td>{{ item.no }}</td>
                <td>{{ item.texto }}</td>
                <td><input type="radio" :name="'med-'+index" value="Siempre" v-model="respuestas3[index]" /></td>
                <td><input type="radio" :name="'med-'+index" value="Semana" v-model="respuestas3[index]" /></td>
                <td><input type="radio" :name="'med-'+index" value="Mes" v-model="respuestas3[index]" /></td>
                <td><input type="radio" :name="'med-'+index" value="Cuatrimestre" v-model="respuestas3[index]" /></td>
                <td><input type="radio" :name="'med-'+index" value="Nunca" v-model="respuestas3[index]" /></td>
              </tr>
            </tbody>
          </table>

          <h3>4.- Marca con una "X" la respuesta que consideres adecuada</h3>
          <table>
            <thead>
              <tr>
                <th>Descripción</th>
                <th>Buena combinación</th>
                <th>Demasiada teoría</th>
                <th>Mucho práctico</th>
                <th>Ni teoría ni práctica</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>El curso estuvo:</td>
                <td><input type="radio" name="teo" value="combinado" v-model="respuesta4" /></td>
                <td><input type="radio" name="teo" value="teoria" v-model="respuesta4" /></td>
                <td><input type="radio" name="teo" value="practico" v-model="respuesta4" /></td>
                <td><input type="radio" name="teo" value="ni" v-model="respuesta4" /></td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- 3. Autoevaluación del Alumno -->
        <div v-if="step === 3">
          <h3>5.- Autoevaluación del Alumno</h3>
          <table>
            <thead>
              <tr>
                <th>No.</th>
                <th>Concepto</th>
                <th>Siempre</th>
                <th>Semana</th>
                <th>Mes</th>
                <th>Cuatrimestre</th>
                <th>Nunca</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in autoevaluacion" :key="index">
                <td>{{ index + 1 }}</td>
                <td>{{ item }}</td>
                <td><input type="radio" :name="'auto-'+index" value="Siempre" v-model="respuestas5[index]" /></td>
                <td><input type="radio" :name="'auto-'+index" value="Semana" v-model="respuestas5[index]" /></td>
                <td><input type="radio" :name="'auto-'+index" value="Mes" v-model="respuestas5[index]" /></td>
                <td><input type="radio" :name="'auto-'+index" value="Cuatrimestre" v-model="respuestas5[index]" /></td>
                <td><input type="radio" :name="'auto-'+index" value="Nunca" v-model="respuestas5[index]" /></td>
              </tr>
            </tbody>
          </table>

          <h3>6.- Comentarios</h3>
          <label>Fortalezas del docente y sugerencias para mejorar las clases:</label>
          <textarea v-model="comentario1" rows="4"></textarea>

          <label>¿Consideras necesario realizar algún otro comentario respecto a tu docente?</label>
          <textarea v-model="comentario2" rows="3"></textarea>
        </div>

        <!-- BOTÓN -->
        <div class="boton-container">
          <button type="submit" class="boton-azul">
            {{ step < 3 ? 'Siguiente' : 'Enviar' }}
          </button>
        </div>
      </form>
    </main>
  </MenuAlumno>
</template>

<script setup>
import { ref } from 'vue'
import MenuAlumno from '@/layouts/MenuAlumno.vue'

const step = ref(1)

const facilitador = [
  { no: 1, texto: "Orientó sobre unidades de aprendizaje." }, { no: 2, texto: "Objetivos claros." },
  { no: 3, texto: "Domina contenidos." }, { no: 4, texto: "Resumió temas." }
]
const habilidades = [
  { no: 1, texto: "Lenguaje apropiado" }, { no: 2, texto: "Desarrollo profesional" },
  { no: 3, texto: "Capta atención" }, { no: 4, texto: "Relación con competencias" }
]
const medios = [
  { no: 1, texto: "Pizarrón" }, { no: 2, texto: "Cañón" },
  { no: 3, texto: "Webquest" }, { no: 4, texto: "Otro medio" }, { no: 5, texto: "Otro más" }
]
const autoevaluacion = [
  "¿Participé en clase?", "¿Falté a alguna clase?", "¿Realicé todos los trabajos?",
  "¿Solicité asesoría?", "¿Apliqué técnicas de autoestudio?", "¿Investigación?",
  "¿Material necesario?", "¿Me preparé para exámenes?", "¿Puse en práctica?",
  "¿Atención y disposición?", "Competencias"
]

// Respuestas
const respuestas1 = ref(Array(facilitador.length).fill(null))
const respuestas2 = ref(Array(habilidades.length).fill(null))
const respuestas3 = ref(Array(medios.length).fill(null))
const respuestas5 = ref(Array(autoevaluacion.length).fill(null))
const comentario1 = ref('')
const comentario2 = ref('')

function handleSubmit() {
  if (step.value < 3) {
    step.value++
  } else {
    console.log("Evaluación enviada")
  }
}
</script>

<style src="@/../css/Registros.css"></style>
<style src="@/../css/botones.css"></style>
<style src="@/../css/Evaluacion.css"></style>
