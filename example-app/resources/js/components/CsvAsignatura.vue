<template>
  <!-- Modal para cargar asignaturas desde un archivo CSV -->
  <div class="modal-csv">
    <div class="modal-content">
      <div class="csv-container">
        <h1>Cargar Materias por CSV</h1>

        <!-- Área de carga de archivo -->
        <div class="csv-upload">
          <input type="file" accept=".csv" @change="handleFileUpload" />
          <p>Formato: <strong>nombre_materia</strong></p>
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

// Captura el archivo seleccionado
const handleFileUpload = (event) => {
  file.value = event.target.files[0]
}

// Enviar archivo al backend
const cargarCSV = async () => {
  if (!file.value) {
    alert('Por favor, selecciona un archivo CSV.')
    return
  }

  const formData = new FormData()
  formData.append('archivo', file.value)

  try {
    const response = await axios.post('/api/materias/csv', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })

    alert(response.data.message || 'Materias cargadas correctamente.')
    emit('guardado')
    emit('cerrar')
  } catch (error) {
    console.error(error)
    if (error.response?.data?.errores) {
      const mensajes = error.response.data.errores.map(e => `Línea ${e.linea}: ${e.errores.join(', ')}`)
      alert(mensajes.join('\n'))
    } else {
      alert('Error al cargar las materias.')
    }
  }
}
</script>

<!-- Estilos específicos para carga CSV -->
<style src="@/../css/RegistroCSV.css"></style>
