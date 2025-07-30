<template>
  <teleport to="body">
    <div class="modal-overlay" @click.self="cerrar">
      <div class="modal-content">
        <h2>Actualizar</h2>

        <div class="form-group">
          <label>No°:</label>
          <input type="number" v-model="form.num" placeholder="1-10" />
        </div>

        <div class="form-group">
          <label>Cuatrimestre:</label>
          <input type="text" v-model="form.nombre" placeholder="Primer Cuatrimestre" />
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
const props = defineProps({
  relacion: Object
})
const emit = defineEmits(['cerrar', 'guardar'])

const form = reactive({
  num: '',
  nombre: ''
})

// Sincroniza la prop con el formulario cuando cambie
watch(() => props.relacion, (nuevo) => {
  form.num = nuevo.num
  form.nombre = nuevo.nombre
}, { immediate: true })

function cerrar() {
  emit('cerrar')
}

function guardar() {
  emit('guardar', { ...form })
}
</script>


<style src="@/../css/Editar.css"></style>