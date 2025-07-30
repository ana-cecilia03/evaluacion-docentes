<template>
  <teleport to="body">
    <div class="modal-overlay" @click.self="cerrar">
      <div class="modal-content">
        <h2>Actualizar</h2>

        <div class="form-group">
          <label for="carrera">Carrera:</label>
          <select id="carrera" v-model="form.carrera">
            <option disabled value="">Seleccione una carrera</option>
            <option>Ingeniería en sistemas</option>
            <option>Ingeniería industrial</option>
            <option>Ingeniería civil</option>
          </select>
        </div>

        <div class="form-group">
          <label for="cuatri">Cuatrimestre:</label>
          <select id="cuatri" v-model="form.cuatrimestre">
            <option disabled value="">Seleccione cuatrimestre</option>
            <option v-for="n in 12" :key="n" :value="n">{{ n }}</option>
          </select>
        </div>

        <div class="form-group">
          <label for="materia">Materia:</label>
          <select id="materia" v-model="form.materia">
            <option disabled value="">Seleccione una materia</option>
            <option>Programación</option>
            <option>Matemáticas</option>
            <option>Física</option>
          </select>
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
import { reactive } from 'vue'

const emit = defineEmits(['cerrar', 'guardar'])

const form = reactive({
  carrera: '',
  cuatrimestre: '',
  materia: ''
})

function cerrar() {
  emit('cerrar')
}

function guardar() {
  emit('guardar', { ...form })
}
</script>

<style src="@/../css/Editar.css"></style>