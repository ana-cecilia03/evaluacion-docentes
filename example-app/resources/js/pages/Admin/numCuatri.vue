<template>
  <Menu>
    <main class="contenido-principal">

      <!-- Filtros y botón Agregar -->
      <div class="relacion-filtros">
        <div class="relacion-columna">
            <label>Número:</label>
          <input type="number" placeholder="1-10"/>
        </div>

        <div class="relacion-columna">
            <label>Nombre:</label>
           <input type="text" placeholder="Primer Cuatrimestre" />
        </div>

        <div class="relacion-columna">
            <label >   </label>
          <button class="btn-agregar">Agregar</button>
        </div>
      </div>

      <!-- Tabla -->
        <div class="table-wrapper">
        <table>
          <thead>
            <tr>
              <th>No°</th>
              <th>Cuatrimestre</th>
              <th>Acción</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>5</td>
              <td>Segundo Cuatrimestre</td>
              <td>
                <button class="boton-verde" >Editar</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </main>
  </Menu>
</template>

<script setup>
import Menu from '@/layouts/Menu.vue'

</script>

<style src="@/../css/Registros.css"></style>
<style src="@/../css/relacion.css"></style>
<style src="@/../css/botones.css"></style>
