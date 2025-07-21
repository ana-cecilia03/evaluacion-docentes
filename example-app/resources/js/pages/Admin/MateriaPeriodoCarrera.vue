<template>
  <Menu>
    <main class="contenido-principal">

      <!-- Barra de acciones -->
      <div class="actions">
        <input type="text" placeholder="Buscar periodo..."  />
        <select>
          <option value="">Filtrar por carrera</option>
          <option>sistemas COMPUTACIONALES</option>
          <option>ROBOTICA</option>
          <option>LOGISTICA</option>
          <option>TELECOMUNICACIONES</option>
        </select>
        <button class="btn-register" @click="mostrarFormulario = true">Agregar</button>
        <button class="btn-upload" @click="mostrarCSV = true">Cargar CSV</button>
      </div>

      <!-- Modal: Registro Manual -->
      <div v-if="mostrarFormulario" class="modal-overlay">
        <div class="modal-content">
          <AlumnosManual @cerrar="mostrarFormulario = false" @guardado="cargarAlumnos" />
        </div>
      </div>

      <!-- Modal: Carga CSV -->
      <div v-if="mostrarCSV" class="modal-csv">
        <div class="modal-content">
          <CsvAlumnos @cerrar="mostrarCSV = false" @guardado="cargarAlumnos" />
        </div>
      </div>

      <!-- Modal: Edición -->
      <div v-if="editarDatos" class="modal-overlay">
        <div class="modal-content">
          <EditarAlumno
            :alumno="alumnoSeleccionado"
            @cerrar="editarDatos = false"
            @actualizado="cargarAlumnos"
          />
        </div>
      </div>

      <div class="table-wrapper">
        <table>
          <thead>
            <tr>
              <th>Periodo</th>
              <th>Materia</th>
              <th>Carrera</th>
              <th>Acción</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>5</td>
              <td>Fisica</td>
              <td>Ingenieria en sistemas</td>
              <td>
                <button class="btn-edit" >Editar</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </main>
  </Menu>
</template>

<script setup>
import Menu from '@/layouts/Menu.vue'

</script>

<style src="@/../css/Registros.css"></style>
<style src="@/../css/botones.css"></style>
