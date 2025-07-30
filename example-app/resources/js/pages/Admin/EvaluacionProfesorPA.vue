<template>
  <Menu>
    <main class="contenido-principal evaluacion-page">
      <!-- CABECERA -->
      <header class="encabezado-evaluacion">
        <h1 class="titulo">Evaluación de Desempeño</h1>
        
        <div class="acciones">
          <div class="download-wrapper">
            <button class="boton-verde" @click="toggleDropdown">
              Descargar
            </button>
            <ul v-if="showDownload" class="dropdown">
              <li @click="download('pdf')">PDF</li>
              <li @click="download('xlsx')">Excel</li>
            </ul>
          </div>
        </div>

        <div class="datos-grid">
          <div class="col-izq">
            <div class="campo" v-for="(item, index) in ['Nombre', 'Puesto', 'Evaluador', 'Periodo']" :key="index">
              <label>{{ item }}:</label>
              <input :value="form[item.toLowerCase()]" type="text" disabled />
            </div>
          </div>

          <div class="col-mid">
            <div class="box-calif">
              <span class="titulo-box">Calificación I <br />Resp. PE </span>
              <input
                v-model.number="califI"
                type="number"
                step="0.1"
                min="0"
                max="10"
                class="input-calif"
              />
            </div>
            <div class="box-calif">
              <span class="titulo-box">Calificación II <br />ESTUDIANTE</span>
              <input
                v-model.number="califII"
                type="number"
                step="0.1"
                min="0"
                max="10"
                class="input-calif"
              />
            </div>
          </div>

          <div class="col-der">
            <div class="total-box">
              <span class="total">{{ calificacionFinal.toFixed(1) }}</span>
            </div>
          </div>
        </div>
      </header>

      <!-- TABLA -->
      <div class="table-wrapper">
        <table class="tabla-evaluacion">
          <thead>
            <tr>
              <th>Factor</th>
              <th>Definición</th>
              <th>Calificación</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="(item, index) in tablaDatos" :key="index">
              <td>{{ item.factor }}</td>
              <td>{{ item.definicion }}</td>
              <td>
                <input
                  type="number"
                  v-model.number="item.calificacion"
                  class="input-calif"
                  step="0.1"
                  min="0"
                  max="10"
                />
              </td>
            </tr>
          </tbody>

          <!-- FOOTER de tabla -->
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
                      <td rowspan="2" class="sub-total-titulo">
                        Sub.<br />total
                      </td>
                      <td>{{ promedioTabla.toFixed(1) }}</td>
                      <td>{{ (promedioTabla / 2).toFixed(1) }}</td>
                    </tr>
                    <tr>
                      <td colspan="2" class="calif-final-titulo">
                        Calificación:
                      </td>
                    </tr>
                    <tr>
                      <td colspan="3" class="calif-final">
                        {{ calificacionFinal.toFixed(1) }}
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
        <div class="nombre-firma">Nombre y firma</div>
      </section>
    </main>
  </Menu>
</template>

<script>
import Menu from '@/layouts/Menu.vue';

export default {
  components: {
    Menu
  },
  data() {
    return {
      showDownload: false,
      form: {
        nombre: "Juan Pérez",
        puesto: "Desarrollador",
        evaluador: "Carlos López",
        periodo: "2023"
      },
      califI: 0.0,
      califII: 0.0,
      comentario: "",
      tablaDatos: [
        { factor: "Trabajo en equipo", definicion: "Capacidad de colaborar con otros", calificacion: 0.0 },
        { factor: "Creatividad", definicion: "Generación de ideas nuevas", calificacion: 0.0 }
      ]
    };
  },
  computed: {
    promedioTabla() {
      const total = this.tablaDatos.reduce((sum, item) => sum + item.calificacion, 0);
      return total / this.tablaDatos.length;
    },
    calificacionFinal() {
      // Ya no calculamos el promedio de califI y califII, puedes cambiar este cálculo si tienes otra fórmula
      return this.promedioTabla; // O bien, dejarlo con otro valor estático o fórmula distinta
    }
  },
  methods: {
    toggleDropdown() {
      this.showDownload = !this.showDownload;
    },
    download(format) {
      console.log(`Descargando en formato: ${format}`);
    }
  }
};
</script>

<style src="@/../css/botones.css"></style>
<style src="@/../css/EvaluacionProfesores.css"></style>
