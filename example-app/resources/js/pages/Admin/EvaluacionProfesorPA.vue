<template>
  <Menu>
    <main class="contenido-principal">
      <form @submit.prevent="handleSubmit">
        <div v-if="step === 1">
          <h3>Evaluación de Factores</h3>

          <!-- Filtro de tipo de profesor -->
          <div class="filtro-tipo-profesor">
            <label>Tipo de Profesor: </label>
            <button
              type="button"
              :class="{ active: tipoProfesor === 'PA' }"
              @click="tipoProfesor = 'PA'"
            >
              PA
            </button>
            <button
              type="button"
              :class="{ active: tipoProfesor === 'PTC' }"
              @click="tipoProfesor = 'PTC'"
            >
              PTC
            </button>
          </div>

          <div class="table-wrapper">
            <table>
              <thead>
                <tr>
                  <th>No.</th>
                  <th>Factor</th>
                  <th>Definición</th>
                  <th>Calificación</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(item, index) in tablaFiltrada" :key="index">
                  <td>{{ index + 1 }}</td>
                  <td class="factor-text">{{ item.factor }}</td>
                  <td>{{ item.definicion }}</td>
                  <td>{{ item.calificacion }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </form>
    </main>
  </Menu>
</template>

<script setup>
import { ref, computed } from 'vue'
import Menu from '@/layouts/Menu.vue'

const step = ref(1)
const tipoProfesor = ref('PTC') // inicia seleccionado con PTC

const tablaDatos = [
  {
    factor: "Elaboración de plan curso, avance programático y evidencias",
    definicion:
      "Elaborar los documentos establecidos en el manual de procedimientos de manera eficiente y con un diseño adecuado que facilite la toma de decisiones para mejorar el proceso de enseñanza-aprendizaje. Realiza el registro de documentos de manera correcta, con información concreta, relevante y veraz, previamente revisada y autorizada, para ser entregada en el tiempo programado. Los resultados son satisfactorios.",
    calificacion: 4,
  },
  {
    factor: "Evaluación del aprendizaje por competencias EC, ED y EP",
    definicion:
      "Establecer las competencias, unidades, y aprendizajes esperados del programa de estudio, planear, evaluar y realizar materiales didácticos y estrategias que ayuden al estudiante la comprensión cabal del temario, dar seguimiento a su desempeño, detectar la presencia de problemas y determinar parámetros de evaluación y acreditación de los estudiantes.",
    calificacion: 5,
  },
  {
    factor: "Investigación",
    definicion:
      "Desarrollar la investigación en grupos interdisciplinarios y multidisciplinarios en torno a programas y proyectos que contribuyan a la solución de problemas locales, regionales y nacionales. Publicar o difundir los resultados de investigación conforme a la normativa vigente. Participar en congresos, seminarios y conferencias para la presentación de avances y resultados. Asesorar y dirigir tesis de licenciatura. Participar en redes de investigación locales, nacionales e internacionales.",
    calificacion: 2,
  },
  {
    factor: "Tutorías",
    definicion:
      "Elaborar un diagnóstico detallado de la situación académica de los estudiantes asignados para otorgarles el servicio de tutoría. A partir de dicho diagnóstico, estructurar un plan tutorial que permita intervenir eficazmente en las problemáticas detectadas. Se debe brindar atención a través de tutorías individuales, adecuándose a las necesidades específicas de cada estudiante. Además, es responsabilidad del tutor elaborar y controlar los formatos establecidos por el programa de asesorías y tutorías de la UPTEX.",
    calificacion: 4,
  },
  {
    factor: "Asesorías",
    definicion:
      "Orientar al estudiante para favorecer la perdurabilidad de lo aprendido, la comprensión significativa de los contenidos, así como la adquisición de habilidades, actitudes y valores. Además, promover la autorregulación conductual y el aprendizaje autónomo. Esta orientación consiste en un conjunto de actividades dedicadas a la formación académica del estudiante, guiándolo hacia las opciones más adecuadas para su estudio y aprendizaje significativo. También asegurar el cumplimiento de procedimientos institucionales.",
    calificacion: 4,
  },
  {
    factor: "Utilización de plataformas",
    definicion:
      "Establecer las competencias, unidades, y aprendizajes esperados del programa de estudio, planear, evaluar y realizar materiales didácticos y estrategias que ayuden al estudiante la comprensión cabal del temario, dar seguimiento a su desempeño, detectar la presencia de problemas y determinar parámetros de evaluación y acreditación de los estudiantes.",
    calificacion: 4,
  },
  {
    factor: "Solución de Problemas",
    definicion:
      "Capacidad de identificar, analizar y definir los elementos significativos que constituyen un problema, evaluarlos y resolverlo con juicio y criterio encontrando soluciones viables y efectivas.",
    calificacion: 5,
  },
  {
    factor: "Puntualidad",
    definicion:
      "Se presenta en su horario laboral y realiza las actividades encomendadas a su debido tiempo. Asiste a las juntas programadas desarrollando las actividades encomendadas con responsabilidad.",
    calificacion: 4,
  },
  {
    factor: "Trabajo en Equipo",
    definicion:
      "Trabaja eficazmente colaborando con otros para lograr un objetivo común, además de compartir la información y fomentar la productividad.",
    calificacion: 4,
  },
]

const tablaFiltrada = computed(() => {
  if (tipoProfesor.value === 'PA') {
    return tablaDatos.filter(
      item =>
        item.factor !== 'Investigación' &&
        item.factor !== 'Tutorías' &&
        item.factor !== 'Asesorías'
    )
  }
  return tablaDatos
})

function handleSubmit() {
  if (step.value === 1) step.value = 2
}
</script>

<style src="@/../css/EvaluacionProfesores.css"></style>
<style src="@/../css/botones.css"></style>