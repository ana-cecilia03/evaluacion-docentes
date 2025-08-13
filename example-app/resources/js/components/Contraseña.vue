<template>
  <!-- Modal de cambio de contraseña -->
  <div class="modal-cuadro">
    <div class="modal-password">
      <h2>Contraseña</h2>

      <!-- Campo: Nueva contraseña -->
      <label>Nueva contraseña:</label>
      <div class="password-input">
        <input :type="mostrar1 ? 'text' : 'password'" placeholder="Contraseña" v-model="nueva" />
        <span @click="mostrar1 = !mostrar1">👁️</span>
      </div>

      <!-- Campo: Repetir contraseña -->
      <label>Repetir contraseña:</label>
      <div class="password-input">
        <input :type="mostrar2 ? 'text' : 'password'" placeholder="Contraseña" v-model="repetir" />
        <span @click="mostrar2 = !mostrar2">👁️</span>
      </div>

      <!-- Botones -->
      <div class="boton-group">
        <button class="boton-rojo" @click="$emit('cerrar')">Cancelar</button>
        <button class="boton-verde" @click="guardar">Aceptar</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

// Variables de estado locales
const nueva = ref('')
const repetir = ref('')
const mostrar1 = ref(false)
const mostrar2 = ref(false)

// Esta función luego se conectará al backend (ej. API para actualizar contraseña)
const guardar = () => {
  if (nueva.value !== repetir.value) {
    alert('Las contraseñas no coinciden.')
    return
  }

  // Aquí se podría hacer una solicitud POST al backend
  alert('Contraseña actualizada correctamente.')
}
</script>

<!-- Estilos -->
