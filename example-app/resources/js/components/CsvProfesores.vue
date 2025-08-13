<template>
  <!-- Modal para cargar profesores desde CSV -->
  <div class="modal-csv">
    <div class="modal-content">
      <div class="csv-container">
        <h1>Cargar Profesores por CSV</h1>

        <!-- Área de carga del archivo -->
        <div class="csv-upload">
          <input type="file" accept=".csv" @change="handleFileUpload" />
          <p>
            Formato esperado:
            <strong>matricula,nombre_completo,correo,password,cargo,status</strong><br>
            (La columna <code>password</code> es opcional. Si no se incluye, se usará la matrícula).
          </p>
        </div>

        <!-- Mensaje de error -->
        <div v-if="error" class="error-message">
          <pre>{{ error }}</pre>
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
import axios from 'axios'

const emit = defineEmits(['cerrar', 'guardado'])

const file = ref(null)
const error = ref(null)

// Captura el archivo seleccionado
const handleFileUpload = (event) => {
  file.value = event.target.files[0]
}

// Enviar el archivo CSV al backend
const cargarCSV = async () => {
  error.value = null

  if (!file.value) {
    alert('Por favor, selecciona un archivo CSV.')
    return
  }

  const formData = new FormData()
  formData.append('archivo', file.value)

  try {
    const response = await axios.post('/api/profesores/csv', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    })

    alert('Profesores cargados correctamente.')
    emit('guardado')
    emit('cerrar')
  } catch (err) {
    console.error('Error al cargar CSV:', err)

    if (err.response?.data?.errores) {
      // Mostrar errores detallados por línea
      error.value = err.response.data.errores
        .map(e => `Línea ${e.linea}: ${e.errores.join(', ')}`)
        .join('\n')
    } else if (err.response?.data?.message) {
      error.value = err.response.data.message
    } else {
      error.value = 'Error desconocido al cargar el archivo.'
    }
  }
}
</script>

<style src="@css/global.css" lang="css"></style>
