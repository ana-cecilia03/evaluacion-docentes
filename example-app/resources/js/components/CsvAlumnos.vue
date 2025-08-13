<template>
  <div class="modal-csv">
    <div class="modal-content">
      <div class="csv-container">
        <h1>Cargar Alumnos por CSV</h1>

        <!-- Área de carga de archivo -->
        <div class="csv-upload">
          <input type="file" accept=".csv" @change="handleFileUpload" />
          <p>
            Formato esperado:<br />
            <strong>
              matricula,nombre_completo,correo,grupo,status,password
            </strong>
            <br />
            <em>* El campo <code>grupo</code> debe ser el ID del grupo (número), no su nombre.</em><br />
            <em>* El campo <code>password</code> es opcional. Si se omite, se usará la matrícula como contraseña.</em>
          </p>
        </div>

        <!-- Botones -->
        <div class="csv-buttons">
          <button @click="$emit('cerrar')" class="cancel-button">Cancelar</button>
          <button @click="cargarCSV" class="cargar-button">Cargar CSV</button>
        </div>

        <!-- Mostrar errores globales -->
        <div v-if="error" class="error-message">
          {{ error }}
        </div>

        <!-- Mostrar errores detallados por línea -->
        <div v-if="errores.length" class="errores-detallados">
          <h3>Errores en la carga:</h3>
          <ul>
            <li v-for="(e, index) in errores" :key="index">
              Línea {{ e.linea }}: {{ e.errores.join(', ') }}
            </li>
          </ul>
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
const errores = ref([])

const emit = defineEmits(['cerrar', 'guardado'])

const handleFileUpload = (event) => {
  file.value = event.target.files[0]
  error.value = null
  errores.value = []
}

const cargarCSV = async () => {
  error.value = null
  errores.value = []

  if (!file.value) {
    error.value = 'Por favor, selecciona un archivo CSV.'
    return
  }

  const formData = new FormData()
  formData.append('archivo', file.value)

  try {
    const response = await axios.post('/api/alumnos/csv', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })

    alert(response.data.message || 'Archivo cargado correctamente')
    emit('guardado')
    emit('cerrar')
  } catch (err) {
    if (err.response?.status === 422 && err.response.data?.errores) {
      errores.value = err.response.data.errores
      error.value = err.response.data.message || 'Errores en el archivo CSV.'
    } else {
      error.value = err.response?.data?.message || 'Error al subir el archivo CSV.'
    }
    console.error('Error al subir CSV:', err)
  }
}
</script>

