<template>
  <Menu>
    <div class="contenido-periodo">
      <!-- Contenedor principal con contorno azul claro -->
      <div class="contenedor-cuadro">
        <h1 class="titulo">Crear periodo</h1>
        
        <form class="formulario-periodo" @submit.prevent="crearPeriodo">
          <div class="campo-formulario">
            <label for="numero">Número:</label>
            <input type="number" id="numero" v-model="form.numero" required />
          </div>

          <div class="campo-formulario">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" v-model="form.nombre" required />
          </div>

          <div class="campo-formulario">
            <label for="inicio">Fecha de inicio:</label>
            <input type="date" id="inicio" v-model="form.inicio" required />
          </div>

          <div class="campo-formulario">
            <label for="fin">Fecha de término:</label>
            <input type="date" id="fin" v-model="form.fin" required />
          </div>

          <div class="botones-formulario">
            <button type="button" class="btn-cancelar" @click="cancelar">Cancelar</button>
            <button type="submit" class="btn-crear">Crear</button>
          </div>
        </form>
      </div>
    </div>
  </Menu>
</template>

<script setup>
import { ref } from 'vue';
import Menu from '@/layouts/Menu.vue'

// Definir las propiedades reactivas con ref()
const form = ref({
  numero: '',
  nombre: '',
  inicio: '',
  fin: ''
});

// Funciones del componente
function crearPeriodo() {
  console.log("Periodo creado:", form.value);
  // Aquí va el código para enviar al backend
}

function cancelar() {
  form.value = { numero: '', nombre: '', inicio: '', fin: '' };
}
</script>

<style src="@/../css/DashboardAdmin.css" scoped></style>
