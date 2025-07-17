<template>
  <!-- Modal para cargar asignaturas desde un archivo CSV -->
  <div class="modal-csv">
    <div class="modal-content">
      <div class="csv-container">
        <h1>Cargar Asignaturas por CSV</h1>

        <!-- Área de carga de archivo -->
        <div class="csv-upload">
          <input type="file" accept=".csv" @change="handleFileUpload" />
          <p>Formato: <strong>nombre, departamento, semestre, duracion_semanas</strong></p>
        </div>

        <!-- Botones para cancelar o confirmar carga -->
        <div class="csv-buttons">
          <button @click="$emit('cerrar')" class="cancel-button">Cancelar</button>
          <button @click="cargarCSV" class="cargar-button">Cargar CSV</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

// Archivo CSV seleccionado
const file = ref(null)

// Captura el archivo cargado
const handleFileUpload = (event) => {
  file.value = event.target.files[0]
}

// Acción al presionar "Cargar CSV"
const cargarCSV = () => {
  if (!file.value) {
    alert('Por favor, selecciona un archivo CSV.')
    return
  }

  // Aquí se integrará el envío al backend en el futuro
  console.log('Archivo seleccionado:', file.value)
  alert('Archivo de asignaturas cargado correctamente')
}
</script>

<!-- Estilos específicos para carga CSV -->
<style src="@/../css/RegistroCSV.css"></style>
