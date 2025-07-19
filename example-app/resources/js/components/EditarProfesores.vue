<template>
  <!-- Modal para registrar un profesor manualmente -->
  <div class="modal-overlay">
    <div class="modal-content">
      <div class="register-container">
        <h1 class="titulo">Actualizar</h1>

        <!-- Formulario de registro -->
        <form @submit.prevent="registrarProfesor" class="register-form">

          <div class="form-group">
            <label for="matriculo"> Matricula</label>
            <input type="text" id="matriculo" v-model="profesor.matricula" placeholder="Ej. 13122593103" />
          </div>

          <!-- Campo: Nombre completo -->
          <div class="form-group">
            <label for="nombre">Nombre completo</label>
            <input
              type="text"
              id="nombre"
              v-model="profesor.nombre"
              placeholder="Ej. Laura Martínez"
            />
          </div>


          <!-- Campo: Correo electrónico -->
          <div class="form-group">
            <label for="curp">Curp</label>
            <input type="curp" id="curp" v-model="profesor.curp" placeholder="MDFI32URAS9RMVS7" />
          </div>

          <!-- Campo: ESTADO -->
          <div class="form-group">
            <label for="estado">Estado</label>
            <select id="estado" v-model="profesor.estado">
              <option value="">Seleccione</option>
              <option>Activo</option>
              <option>Inactivo</option>
            </select>
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

// Estado del formulario
const profesor = ref({
  matricula: '',
  nombre: '',
  curp: '',
  estado: ''
})

// Acción de registrar (temporal)
const registrarProfesor = () => {
  // Aquí se conectará al backend más adelante
  console.log('Datos del profesor:', profesor.value)
  alert('Profesor registrado correctamente')
}
</script>

<!-- Estilos del formulario de registro -->
<style src="@/../css/RegistroManual.css"></style>
