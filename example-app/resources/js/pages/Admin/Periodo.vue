<template>
  <Menu>
    <div class="contenido-periodo">
      <h1 class="titulo">Historial de periodos</h1>
        <div class="table-wrapper">
          <table class="tabla-periodos">
            <thead>
              <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>Fecha de inicio</th>
                <th>Fecha de termino</th>
                <th></th>
                <th>Acciones</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>1</td>
                <td>Primer cuatrimestre</td>
                <td>dd/mm/aa</td>
                <td>dd/mm/aa</td>
                <td><button class="boton-verde">Activar</button></td>
                <td><button class="boton-rojo">Desactivar</button></td>
              </tr>
            </tbody>
          </table>
      </div>
    </div>
  </Menu>
</template>

<script setup>
import Menu from '@/layouts/Menu.vue'
</script>

<style src="@/../css/Contenido.css"></style>
<style src="@/../css/botones.css"></style>
