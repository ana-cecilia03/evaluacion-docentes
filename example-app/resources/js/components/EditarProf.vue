<template>
  <teleport to="body">
    <div class="modal-overlay" @click.self="cerrar">
      <div class="modal-content">
        <h2>Actualizar</h2>

        <div class="form-group">
          <label for="profesor">Profesor:</label>
          <select id="profesor">
            <option disabled value="">Seleccione un profesor</option>
            <option>Ivan Osoria</option>
            <option>Edurnet Jaquelin</option>
          </select>
        </div>         

        <div class="form-group">
          <label for="periodo">Periodo:</label>
          <select id="periodo">
            <option disabled value="">Seleccione el periodo</option>
            <option>1</option>
            <option>2</option>
            <option>3</option>
          </select>
        </div>         

        <div class="form-group">
          <label for="carrera">Carrera:</label>
          <select id="carrera">
            <option disabled value="">Seleccione una carrera</option>
            <option>Ingeniería en sistemas</option>
            <option>Ingeniería industrial</option>
            <option>Ingeniería civil</option>
          </select>
        </div>

        <div class="form-group">
          <label for="materia">Materia:</label>
          <select id="materia" >
            <option disabled value="">Seleccione una materia</option>
            <option>Programación</option>
            <option>Matemáticas</option>
            <option>Física</option>
          </select>
        </div>

        <div class="form-group">
          <label for="grupo">Grupo:</label>
          <select id="grupo">
            <option disabled value="">Seleccione un grupo</option>
            <option>MSC1</option>
            <option>TLC</option>
            <option>VSC2</option>
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

function cerrar() {
  emit('cerrar')
}

function guardar() {
  emit('guardar', { ...form })
}
</script>

<style src="@/../css/Editar.css"></style>