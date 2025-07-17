<template>
  <!-- Modal para registrar un profesor manualmente -->
  <div class="modal-overlay">
    <div class="modal-content">
      <div class="register-container">
        <h1>Registrar Profesor Manualmente</h1>

        <!-- Formulario de registro -->
        <form @submit.prevent="registrarProfesor" class="register-form">

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

          <!-- Campo: Departamento -->
          <div class="form-group">
            <label for="departamento">Departamento</label>
            <select id="departamento" v-model="profesor.departamento">
              <option value="">Seleccione</option>
              <option>Matemáticas</option>
              <option>Física</option>
              <option>Lenguaje</option>
              <option>Tecnología</option>
            </select>
          </div>

          <!-- Campo: Correo electrónico -->
          <div class="form-group">
            <label for="correo">Correo electrónico</label>
            <input
              type="email"
              id="correo"
              v-model="profesor.correo"
              placeholder="ejemplo@dominio.com"
            />
          </div>

          <!-- Campo: Teléfono -->
          <div class="form-group">
            <label for="telefono">Teléfono</label>
            <input
              type="tel"
              id="telefono"
              v-model="profesor.telefono"
              placeholder="Ej. 555-1234"
            />
          </div>

          <!-- Botones de acción -->
          <div class="button-group">
            <button type="button" class="register-rojo" @click="$emit('cerrar')">Cancelar</button>
            <button type="submit" class="register-verde">Registrar Profesor</button>
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
  nombre: '',
  departamento: '',
  correo: '',
  telefono: ''
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
