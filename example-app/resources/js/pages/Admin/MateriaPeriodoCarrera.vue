<template>
  <Menu>
    <main class="contenido-principal">

      <!-- Filtros y botón Agregar -->
      <div class="relacion-filtros">
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
            <option value="">Cuatrimestre</option>
            <option>1</option>
            <option>2</option>
            <option>3</option>
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
              <th>Carrera</th>
              <th>Cuatrimestre</th>
              <th>Materia</th>
              <th>Acción</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Ingeniería en Sistemas</td>
              <td>5</td>
              <td>Física</td>
              <td>
                <button class="boton-verde">Editar</button>
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
