<template>
  <Menu>
    <main class="contenido-principal">
      <!-- Barra de acciones: búsqueda, filtro y botones -->
      <div class="actions">
        <input type="text" placeholder="Buscar carrera..." />
        <select>
          <option value="">Filtrar por carrera</option>
          <option>MSC1</option>
          <option>MSC2</option>
        </select>
        <button class="btn-register" @click="mostrarFormulario = true">Registrar Carrera</button>
      </div>

      <!-- Modal para registro manual de alumno -->
      <div v-if="mostrarFormulario" class="modal-overlay">
        <div class="modal-content">
          <!-- Componente que abrirá un formulario más adelante -->
          <CarrerasManual @cerrar="mostrarFormulario = false" />
        </div>
      </div>

      <!-- Tabla de alumnos estática, se conectará con backend después -->
      <div class="table-wrapper">
        <table>
          <thead>
            <tr>
              <th>Clave</th>
              <th>Nombre</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <!-- Estos datos están fijos para prueba visual -->
            <tr>
              <td>MSC1</td>
              <td>Ingenieria en Sistemas Computacionales</td>
              <td>
                <button class="btn-edit">Editar</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </main>
  </Menu>
</template>

<script setup>
// Importación de layout y componentes visuales (sin lógica por ahora)
import { ref } from 'vue'
import Menu from '@/layouts/Menu.vue'
import CarrerasManual from '@/components/CarrerasManual.vue'

// Variables reactivas para mostrar modales (formulario o csv)
const mostrarFormulario = ref(false)
</script>


<style src="@/../css/Registros.css"></style>
