<template>
  <!-- Modal para cargar alumnos desde un archivo CSV -->
  <div class="modal-csv">
    <div class="modal-content">
      <div class="csv-container">
        <h1>Cargar Alumnos por CSV</h1>

        <!-- Área de carga de archivo -->
        <div class="csv-upload">
          <input type="file" accept=".csv" @change="handleFileUpload" />
          <p>Formato esperado: <strong>matricula,nombre_completo,correo,grupo,status</strong></p>
        </div>

        <!-- Botones para cancelar o confirmar carga -->
        <div class="csv-buttons">
          <button @click="$emit('cerrar')" class="cancel-button">Cancelar</button>
          <button @click="cargarCSV" class="cargar-button">Cargar CSV</button>
        </div>

        <!-- Mostrar errores si los hay -->
        <div v-if="error" class="error-message">
          {{ error }}
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'

const file = ref(null)
const error = ref(null)

const handleFileUpload = (event) => {
  file.value = event.target.files[0]
}

const cargarCSV = async () => {
  error.value = null

  if (!file.value) {
    error.value = 'Por favor, selecciona un archivo CSV.'
    return
  }

  const formData = new FormData()
  formData.append('archivo', file.value)

  try {
    await axios.post('/api/alumnos/csv', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      }
    })

    alert('Archivo de alumnos cargado correctamente')
    file.value = null
    // Emitir eventos si es necesario
    // emit('guardado')
    // emit('cerrar')
  } catch (err) {
    if (err.response?.data?.message) {
      error.value = err.response.data.message
    } else {
      error.value = 'Error al subir el archivo CSV.'
    }
    console.error('Error al subir archivo CSV:', err)
  }
}
</script>

<style src="@/../css/RegistroCSV.css"></style>
