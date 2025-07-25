<template>
  <Menu>
    <main class="contenido-principal">

      <!-- Filtros y botón Agregar -->
      <div class="relacion-filtros">

        <div class="relacion-columna">
          <select>
            <option value="">Profesor</option>
            <option>Ivan Osio</option>
            <option>Edurnet Jaquelin</option>
            <option>Edgardo</option>
          </select>
        </div>

        <div class="relacion-columna">
          <select>
            <option value="">Periodo</option>
            <option>1</option>
            <option>2</option>
            <option>3</option>
          </select>
        </div>

        <div class="relacion-columna">
          <select>
            <option value="">Carreras</option>
            <option>Ingeniería en Sistemas</option>
            <option>Robótica</option>
            <option>Logística</option>
            <option>Telecomunicaciones</option>
          </select>
        </div>

        <div class="relacion-columna">
          <select>
            <option value="">Materia</option>
            <option>Física</option>
            <option>Matemáticas</option>
          </select>
        </div>

        <div class="relacion-columna">
          <button class="btn-agregar">Agregar</button>
        </div>
      </div>

      <!-- Tabla -->
        <div class="table-wrapper">
        <table>
          <thead>
            <tr>
              <th>Profesor</th>
              <th>Periodo</th>
              <th>Carrera</th>
              <th>Materia</th>
              <th>Acción</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Ivan Osio</td>
              <td>5</td>
              <td>Ingenieria en sistemas</td>
              <td>Fisica</td>
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
