<template>
  <!-- Modal de registro de grupo manual -->
  <div class="modal-overlay">
    <div class="modal-content">
      <div class="register-container">
        <h1 class="titulo" >Actualizar</h1>

        <!-- Formulario de registro -->
        <form @submit.prevent="registrarGrupo" class="register-form">

          <!-- Campo: clave -->
          <div class="form-group">
            <label for="clave">Clave</label>
            <input type="text" id="clave" v-model="grupo.clave" placeholder="MSC8" />
          </div>

          <!-- Campo: nombre del grupo -->
          <div class="form-group">
            <label for="carrera">Carrear</label>
            <input type="text" id="carrera" v-model="grupo.nombre" placeholder="Ingenieria en robotica" />
          </div>

          <!-- Botones de acción -->
          <div class="button-group">
            <button type="button" class="register-rojo" @click="$emit('cerrar')">Cancelar</button>
            <button type="submit" class="register-verde">Actualizar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

// Estado del grupo a registrar
const grupo = ref({
  nombre: '',
  asignatura: '',
  profesor: ''
})

// Acción al hacer clic en "Registrar Grupo"
const registrarGrupo = () => {
  // Aquí se conectará al backend cuando esté listo
  console.log('Datos del grupo:', grupo.value)
  alert('Grupo registrado correctamente')
}
</script>

<!-- Estilos específicos para el formulario de registro manual -->
<style src="@/../css/EditarDatos.css"></style>
