<template>
  <Menu>
    <main class="contenido-principal">
            <div class="actions">
        <input type="text" placeholder="Buscar profesor o materia..." />
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
              <th>Profesor</th>
              <th>Materia</th>
              <th>Grupo</th>
              <th>Periodo</th>
              <th>Calificacion</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Ivan osio</td>
              <td>Fisica</td>
              <td>MSC1</td>
              <td>enero-abril2025</td>
              <td>6.3</td>
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

