<template>
  <!-- Modal para cargar grupos desde un archivo CSV -->
  <div class="modal-csv">
    <div class="modal-content">
      <div class="csv-container">
        <h1>Cargar Grupos por CSV</h1>

        <!-- Área de carga de archivo -->
        <div class="csv-upload">
          <input type="file" accept=".csv" @change="handleFileUpload" />
          <p>
            Formato esperado:
            <strong>nombre</strong>
          </p>
        </div>

        <!-- Mensaje de error -->
        <div v-if="error" class="error-message">
          <pre>{{ error }}</pre>
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
import axios from 'axios'

const emit = defineEmits(['cerrar', 'guardado'])

const file = ref(null)
const error = ref(null)

const handleFileUpload = (event) => {
  file.value = event.target.files[0]
}

const cargarCSV = async () => {
  error.value = null

  if (!file.value) {
    alert('Por favor, selecciona un archivo CSV.')
    return
  }

  const formData = new FormData()
  formData.append('archivo', file.value)

  try {
    await axios.post('/api/grupos/csv', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })

    alert('Grupos cargados correctamente.')
    emit('guardado')
    emit('cerrar')
  } catch (err) {
    console.error('Error al cargar CSV:', err)
    if (err.response?.data?.errores) {
      error.value = err.response.data.errores.map((e) =>
        `Línea ${e.linea}: ${e.errores.join(', ')}`
      ).join('\n')
    } else if (err.response?.data?.message) {
      error.value = err.response.data.message
    } else {
      error.value = 'Error desconocido al cargar archivo CSV.'
    }
  }
}
</script>

<style src="@/../css/RegistroCSV.css"></style>
