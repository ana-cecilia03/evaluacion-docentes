<template>
  <!-- Modal para cargar grupos desde un archivo CSV -->
  <div class="modal-csv">
    <div class="modal-content">
      <div class="csv-container">
        <h1>Cargar Grupos por CSV</h1>

        <!-- Área de carga de archivo -->
        <div class="csv-upload">
          <input type="file" accept=".csv" @change="handleFileUpload" />
          <p>Formato: <strong>nombre_grupo, asignatura, profesor</strong></p>
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

// Referencia al archivo CSV
const file = ref(null)

// Manejo del evento de carga de archivo
const handleFileUpload = (event) => {
  file.value = event.target.files[0]
}

// Acción al presionar el botón de carga
const cargarCSV = () => {
  if (!file.value) {
    alert('Por favor, selecciona un archivo CSV.')
    return
  }

  // Aquí se conectará al backend más adelante
  console.log('Archivo seleccionado:', file.value)
  alert('Archivo cargado correctamente')
}
</script>

<!-- Estilos para el modal y área de carga CSV -->
<style src="@/../css/RegistroCSV.css"></style>
