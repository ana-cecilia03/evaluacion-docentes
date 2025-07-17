<template>
  <div class="page">
    <!-- Encabezado -->
    <header class="header">
      <img src="/img/logo.png" />
      <div class="contenedor">
        <button class="sesion">Cerrar sesión</button>
      </div>
    </header>
    
    <!-- Contenido dinámico -->
    <main class="contenido-principal">
      <slot></slot>
    </main>

    <!-- Pie de página -->
    <footer class="footer">
      Sistema de encuestas © UNIVERSIDAD POLITÉCNICA DE TEXCOCO
    </footer>
  </div>
</template>

<script setup>
// vacío, ya que aún no se conecta a backend
</script>

<style src="@/../css/Externo.css"></style>
<style src="@/../css/Contenido.css"></style>