<template>
  <Menu>
    <main class="contenido-principal">
      <!-- Barra de acciones: búsqueda y filtro -->
      <div class="actions">
        <input type="text" placeholder="Buscar por nombre o ID" />
        <select class="filtro">
          <option value="">Filtrar por cargo</option>
          <option class="opciones">PTC</option>
          <option class="opciones">PA</option>
        </select>
      </div>

      <!-- Tabla de usuarios -->
      <div class="table-wrapper">
        <table>
          <thead>
            <tr>
              <th>Id</th>
              <th>Nombre</th>
              <th>Cargo</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>20230101</td>
              <td>Ana López</td>
              <td>PTC</td>
              <td>
                <button class="boton-azul">Evaluar</button>
              </td>
            </tr>
            <tr>
              <td>20230102</td>
              <td>Mario Ruiz</td>
              <td>PA</td>
              <td>
                <button class="boton-azul">Evaluar</button>
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
<style src="@/../css/Usuarios.css"></style>
<style src="@/../css/botones.css"></style>