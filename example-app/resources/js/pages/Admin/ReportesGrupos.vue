<template>
  <Menu>
    <main class="contenido-principal">
      <div class="actions">
        <input type="text" placeholder="Buscar grupo..." />
        <select class="filtro">
          <option value="">Filtrar por periodo</option>
          <option class="opciones" >enero-abril2025</option>
          <option class="opciones" >enero-abril2024</option>
        </select>
      </div>
      <div class="table-wrapper">
        <table>
          <thead>
            <tr>
              <th>Grupo</th>
              <th>Materia</th>
              <th>Periodo</th>
              <th>Calificacion</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>MSC1</td>
              <td>Fisica</td>
              <td>enero-abril2025</td>
              <td>9.3</td>
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

