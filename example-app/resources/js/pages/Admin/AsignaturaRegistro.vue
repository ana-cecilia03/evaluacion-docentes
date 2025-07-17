<template>
  <Menu>
    <main class="contenido-principal">
      <!-- Barra de acciones: búsqueda y botones -->
      <div class="actions">
        <input type="text" placeholder="Buscar asignatura" />

        <button class="btn-register" @click="mostrarFormulario = true">Registrar Asignatura Manual</button>
        <button class="btn-upload" @click="mostrarCSV = true">Cargar CSV</button>
      </div>

      <!-- Modal para formulario manual -->
      <div v-if="mostrarFormulario" class="modal-overlay">
        <div class="modal-content">
          <AsignaturasManual @cerrar="mostrarFormulario = false" />
        </div>
      </div>

      <!-- Modal para carga CSV -->
      <div v-if="mostrarCSV" class="modal-csv">
        <div class="modal-content">
          <CsvAsignatura @cerrar="mostrarCSV = false" />
        </div>
      </div>

      <!-- Tabla de asignaturas de ejemplo -->
      <div class="table-wrapper">
        <table>
          <thead>
            <tr>
              <th>Clave</th>
              <th>Nombre</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>MAT101</td>
              <td>Álgebra Lineal</td>
              <td>
                <button class="btn-edit">Editar</button>
                <button class="btn-delete">Eliminar</button>
              </td>
            </tr>
            <tr>
              <td>INF204</td>
              <td>Desarrollo Web</td>
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
// Layout y componentes modales (solo visual, sin lógica backend)
//import { ref } from 'vue'
import Menu from '@/layouts/Menu.vue'
//import AsignaturasManual from '@/components/AsignaturasManual.vue'
//import CsvAsignatura from '@/components/CsvAsignatura.vue'

//const mostrarFormulario = ref(false)
//const mostrarCSV = ref(false)
</script>

<!-- Estilos de registros (ubicados en resources/css/Registros.css) -->
<style src="@/../css/Registros.css"></style>
