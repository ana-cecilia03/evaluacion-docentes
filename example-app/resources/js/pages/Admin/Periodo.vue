<template>
  <Menu>
    <div class="contenido-principal">
      <h1 class="titulo">Activar Encuesta</h1>

      <div class="table-wrapper">
        <table class="tabla-periodos">
          <thead>
            <tr>
              <th>Periodo</th>
              <th></th>
              <th>Acciones</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Primer cuatrimestre</td>
              <td><button class="boton-verde">Activar</button></td>
              <td><button class="boton-rojo">Desactivar</button></td>
              <td><button class="boton-azul">Publicar</button></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </Menu>
</template>

<script setup>
import Menu from '@/layouts/Menu.vue'
// Estilos importados desde assets
</script>

<style src="@/../css/Contenido.css"></style>
<style src="@/../css/botones.css"></style>

