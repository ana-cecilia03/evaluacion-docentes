<template>
  <teleport to="body">
    <div class="modal-overlay" @click.self="cerrar">
      <div class="modal-content">
        <h2>Actualizar Cuatrimestre</h2>

        <div class="form-group">
          <label>No°:</label>
          <input type="number" v-model="form.num" placeholder="1-10" />
          <span class="error" v-if="errores.num">{{ errores.num }}</span>
        </div>

        <div class="form-group">
          <label>Cuatrimestre:</label>
          <input type="text" v-model="form.nombre" placeholder="Primer Cuatrimestre" />
          <span class="error" v-if="errores.nombre">{{ errores.nombre }}</span>
        </div>

        <div class="button-group">
          <button class="editar-rojo" @click="cerrar">Cancelar</button>
          <button class="editar-verde" @click="guardar">Actualizar</button>
        </div>
      </div>
    </div>
  </teleport>
</template>

<script setup>
import { reactive, watch } from 'vue'
import axios from 'axios'

const props = defineProps({
  relacion: Object
})
const emit = defineEmits(['cerrar', 'guardar'])

const form = reactive({
  id_cuatrimestre: null,
  num: '',
  nombre: '',
  modified_by: 'admin'
})

const errores = reactive({
  num: '',
  nombre: ''
})

// Rellena el formulario al recibir la prop
watch(() => props.relacion, (nuevo) => {
  if (nuevo) {
    form.id_cuatrimestre = nuevo.id_cuatrimestre
    form.num = nuevo.num
    form.nombre = nuevo.nombre
    errores.num = ''
    errores.nombre = ''
  }
}, { immediate: true })

function cerrar() {
  emit('cerrar')
}

async function guardar() {
  // Limpiar errores
  errores.num = ''
  errores.nombre = ''

  try {
    await axios.put(`/api/cuatrimestres/${form.id_cuatrimestre}`, form)
    emit('guardar', form)
  } catch (error) {
    if (error.response && error.response.status === 422) {
      const campos = error.response.data.errors
      errores.num = campos.num ? campos.num[0] : ''
      errores.nombre = campos.nombre ? campos.nombre[0] : ''
    } else {
      alert('Error inesperado al actualizar cuatrimestre.')
    }
  }
}
</script>


<style scoped>
.error {
  color: red;
  font-size: 0.85rem;
  margin-top: 4px;
  display: block;
}
</style>
