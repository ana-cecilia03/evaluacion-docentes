<template>
  <!-- Modal para cargar alumnos desde un archivo CSV -->
  <div class="modal-csv">
    <div class="modal-content">
      <div class="csv-container">
        <h1>Cargar Alumnos por CSV</h1>

        <!-- Área de carga de archivo -->
        <div class="csv-upload">
          <input type="file" accept=".csv" @change="handleFileUpload" />
          <p>Formato: <strong>matricula, nombre, carrera, grupo</strong></p>
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

// Variable para almacenar el archivo seleccionado
const file = ref(null)

// Captura el archivo cuando se selecciona
const handleFileUpload = (event) => {
  file.value = event.target.files[0]
}

// Aquí se podrá agregar lógica para enviar el CSV al backend
const cargarCSV = () => {
  if (!file.value) {
    alert('Por favor, selecciona un archivo CSV.')
    return
  }

  // Aquí puedes usar FormData + fetch o axios para enviarlo al backend
  console.log('Archivo seleccionado:', file.value)
  alert('Archivo de alumnos cargado correctamente')
}
</script>

<!-- Estilos específicos para carga de CSV -->
<style src="@/../css/RegistroCSV.css"></style>
