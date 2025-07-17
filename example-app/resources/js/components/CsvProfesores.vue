<template>
  <!-- Modal para cargar profesores desde CSV -->
  <div class="modal-csv">
    <div class="modal-content">
      <div class="csv-container">
        <h1>Cargar Profesores por CSV</h1>

        <!-- Área de carga del archivo -->
        <div class="csv-upload">
          <input type="file" accept=".csv" @change="handleFileUpload" />
          <p>Formato: <strong>nombre, departamento, correo, telefono</strong></p>
        </div>

        <!-- Botones para cerrar o cargar el archivo -->
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

// Referencia al archivo CSV seleccionado
const file = ref(null)

// Captura el archivo cuando el usuario lo selecciona
const handleFileUpload = (event) => {
  file.value = event.target.files[0]
}

// Función que se ejecutará al presionar "Cargar CSV"
const cargarCSV = () => {
  if (!file.value) {
    alert('Por favor, selecciona un archivo CSV.')
    return
  }

  // Aquí se conectará con el backend cuando esté listo
  console.log('Archivo seleccionado:', file.value)
  alert('Archivo de profesores cargado correctamente')
}
</script>

<!-- Estilos para este componente -->
<style src="@/../css/RegistroCSV.css"></style>
