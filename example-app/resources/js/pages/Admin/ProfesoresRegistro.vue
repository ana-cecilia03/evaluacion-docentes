<template>
  <Menu>
    <main class="contenido-principal">
      <!-- Barra de acciones: búsqueda, filtro por materia, botones -->
      <div class="actions">
        <input type="text" placeholder="Buscar por nombre o ID" />
        <select>
          <option value="">Materia</option>
          <option>Matemáticas</option>
          <option>Física</option>
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

      <!-- Tabla con profesores (datos simulados por ahora) -->
      <div class="table-wrapper">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Nombre</th>
              <th>Materia</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>PR001</td>
              <td>Laura Martínez</td>
              <td>Matemáticas</td>
              <td>
                <button class="btn-edit">Editar</button>
                <button class="btn-delete">Eliminar</button>
              </td>
            </tr>
            <tr>
              <td>PR002</td>
              <td>José Hernández</td>
              <td>Física</td>
              <td>
                <button class="btn-edit">Editar</button>
                <button class="btn-delete">Eliminar</button>
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
//import { ref } from 'vue'
import Menu from '@/layouts/Menu.vue'
//import ProfesoresManual from '@/components/ProfesoresManual.vue'
//import CsvProfesores from '@/components/CsvProfesores.vue'

//const mostrarFormulario = ref(false)
//const mostrarCSV = ref(false)
</script>

<!-- Estilos globales reutilizados para tablas y acciones -->
<style src="@/../css/Registros.css"></style>
