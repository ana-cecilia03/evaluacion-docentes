<template>
  <Menu>
    <main class="contenido-principal">
      <!-- Barra de acciones: búsqueda, filtro por materia, botones -->
      <div class="actions">
        <input type="text" placeholder="Buscar por nombre o ID" />
        <select>
          <option value="">Estado</option>
          <option>Activo</option>
          <option>Inactivo</option>
        </select>
        <button class="btn-register" @click="mostrarFormulario = true">Registrar Profesor Manual</button>
        <button class="btn-upload" @click="mostrarCSV = true">Cargar CSV</button>
      </div>

      <!-- Modal para formulario manual -->
      <div v-if="mostrarFormulario" class="modal-overlay">
        <div class="modal-content">
          <ProfesoresManual @cerrar="mostrarFormulario = false" />
        </div>
      </div>

      <!-- Modal para carga CSV -->
      <div v-if="mostrarCSV" class="modal-csv">
        <div class="modal-content">
          <CsvProfesores @cerrar="mostrarCSV = false" />
        </div>
      </div>

      <!-- Modal para actualizar -->
      <div v-if="editarProfesores" class="modal-csv">
        <div class="modal-content">
          <EditarProfesores @cerrar="editarProfesores = false" />
        </div>
      </div>

      <!-- Tabla con profesores (datos simulados por ahora) -->
      <div class="table-wrapper">
        <table>
          <thead>
            <tr>
              <th>Matricula</th>
              <th>Nombre</th>
              <th>Curp</th>
              <th>Estado</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>13211689383</td>
              <td>José Hernández</td>
              <td>MDAU62YNA92GJAB5HQ</td>
              <td>Inactivo</td>
              <td>
                <button class="btn-edit" @click="editarProfesores = true">Editar</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </main>
  </Menu>
</template>

<script setup>
// Script solo de frontend, sin conexión a backend por ahora
import { ref } from 'vue'
import Menu from '@/layouts/Menu.vue'
import ProfesoresManual from '@/components/ProfesoresManual.vue'
import CsvProfesores from '@/components/CsvProfesores.vue'
import EditarProfesores from '@/components/EditarProfesores.vue'

const mostrarFormulario = ref(false)
const mostrarCSV = ref(false)
const editarProfesores = ref(false)
</script>

<!-- Estilos globales reutilizados para tablas y acciones -->
<style src="@/../css/Registros.css"></style>
