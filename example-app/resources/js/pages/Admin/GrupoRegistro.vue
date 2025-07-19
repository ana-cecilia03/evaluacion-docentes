<template>
  <Menu>
    <main class="contenido-principal">
      <!-- Barra de acciones: búsqueda, filtro, botones de registro -->
      <div class="actions">
        <input type="text" placeholder="Buscar grupo..." />
        <button class="btn-register" @click="mostrarFormulario = true">Registrar Grupo Manual</button>
        <button class="btn-upload" @click="mostrarCSV = true">Cargar CSV</button>
      </div>

      <!-- Modal para formulario manual -->
      <div v-if="mostrarFormulario" class="modal-overlay">
        <div class="modal-content">
          <GruposManual @cerrar="mostrarFormulario = false" />
        </div>
      </div>

      <!-- Modal para carga CSV -->
      <div v-if="mostrarCSV" class="modal-csv">
        <div class="modal-content">
          <CsvGrupos @cerrar="mostrarCSV = false" />
        </div>
      </div>

      <!-- Modal para actualizar -->
      <div v-if="editarGrupos" class="modal-overlay">
        <div class="modal-content">
          <EditarGrupos @cerrar="editarGrupos = false" />
        </div>
      </div>

      <!-- Tabla con datos de ejemplo -->
      <div class="table-wrapper">
        <table>
          <thead>
            <tr>
              <th>Clave</th>
              <th>Carrera</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>MSC3</td>
              <td>Ingenieria en Sistemas Computacionales</td>
              <td>
                <button class="btn-edit" @click="editarGrupos = true">Editar</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </main>
  </Menu>
</template>

<script setup>
// Solo frontend: vista de registro de grupos
import { ref } from 'vue'
import Menu from '@/layouts/Menu.vue'
import GruposManual from '@/components/GruposManual.vue'
import CsvGrupos from '@/components/CsvGrupos.vue'
import EditarGrupos from '@/components/EditarGrupos.vue'

const mostrarFormulario = ref(false)
const mostrarCSV = ref(false)
const editarGrupos = ref(false)
</script>

<!-- Estilos globales del módulo de registros -->
<style src="@/../css/Registros.css"></style>
