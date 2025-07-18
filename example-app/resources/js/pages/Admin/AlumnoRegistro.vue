<template>
  <Menu>
    <main class="contenido-principal">
      <!-- Barra de acciones: búsqueda, filtro y botones -->
      <div class="actions">
        <input type="text" placeholder="Buscar alumno..." />
        <select>
          <option value="">Filtrar por grupo</option>
          <option>Grupo A</option>
          <option>Grupo B</option>
        </select>
        <button class="btn-register" @click="mostrarFormulario = true">Registrar Alumno Manual</button>
        <button class="btn-upload" @click="mostrarCSV = true">Cargar CSV</button>
      </div>

      <!-- Modal para registro manual de alumno -->
      <div v-if="mostrarFormulario" class="modal-overlay">
        <div class="modal-content">
          <!-- Componente que abrirá un formulario más adelante -->
          <AlumnosManual @cerrar="mostrarFormulario = false" />
        </div>
      </div>

      <!-- Modal para carga de alumnos por CSV -->
      <div v-if="mostrarCSV" class="modal-csv">
        <div class="modal-content">
          <!-- Componente para subir CSV -->
          <CsvAlumnos @cerrar="mostrarCSV = false" />
        </div>
      </div>

      <!-- Tabla de alumnos estática, se conectará con backend después -->
      <div class="table-wrapper">
        <table>
          <thead>
            <tr>
              <th>Matrícula</th>
              <th>Nombre</th>
              <th>Carrera</th>
              <th>Grupo</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <!-- Estos datos están fijos para prueba visual -->
            <tr>
              <td>20230101</td>
              <td>Ana López</td>
              <td>Ingeniería en Sistemas</td>
              <td>A</td>
              <td>
                <button class="btn-edit">Editar</button>
              </td>
            </tr>
            <tr>
              <td>20230102</td>
              <td>Mario Ruiz</td>
              <td>Ingeniería en Robótica</td>
              <td>B</td>
              <td>
                <button class="btn-edit">Editar</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </main>
  </Menu>
</template>

<script setup>
// Importación de layout y componentes visuales (sin lógica por ahora)
import { ref } from 'vue'
import Menu from '@/layouts/Menu.vue'
import AlumnosManual from '@/components/AlumnosManual.vue'
import CsvAlumnos from '@/components/CsvAlumnos.vue'

// Variables reactivas para mostrar modales (formulario o csv)
const mostrarFormulario = ref(false)
const mostrarCSV = ref(false)
</script>


<style src="@/../css/Registros.css"></style>
