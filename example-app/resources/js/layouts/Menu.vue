<template>
  <div class="page-admin">
    <!-- ===============================
         ENCABEZADO SUPERIOR CON LOGO Y MENÚ HAMBURGUESA
         - El logo redirige al dashboard del admin
         - El menú hamburguesa será funcional en futuro para abrir el menú lateral
       =============================== -->
    <header class="header">
      <a href="/admin/dashboard">
        <img src="/img/logo.png" />
      </a>
      <div class="hamburger">
        <div class="bar"></div>
        <div class="bar"></div>
        <div class="bar"></div>
      </div>
    </header>

    <!-- ===============================
         ÁREA PRINCIPAL DINÁMICA
         - Aquí se mostrará el contenido de cada vista dentro del layout
         - Se inyecta usando el <slot></slot>
       =============================== -->
    <main class="contenido-principal">
      <slot></slot>
    </main>

    <!-- ===============================
         PIE DE PÁGINA FIJO
         - Información institucional
         - Se mantiene en todas las páginas
       =============================== -->
    <footer class="footer-admin">
      Sistema de encuestas © UNIVERSIDAD POLITÉCNICA DE TEXCOCO
    </footer>

    <!-- ===============================
         MENÚ LATERAL (SIMULADO)
         - Lista de opciones del panel de administración
         - Cada ítem será luego reemplazado con <router-link> o <a>
         - Puede controlarse con Vue (v-if, v-show, estados)
       =============================== -->
    <div class="menu">
      <ul>
        <!-- Enlaces directos -->
        <li><a href="/admin/dashboard">Inicio</a></li>
        <li><a href="/admin/periodo">Periodo</a></li>

        <!-- Submenú de Registro -->
        <li>Registros para la plataforma
          <ul class="submenu">
            <li><a href="/registro/alumnos">Alumno</a></li>
            <li><a href="/registro/profesores">Profesores</a></li>
            <li><a href="/registro/asignaturas">Asignaturas</a></li>
            <li><a href="/registro/grupo">Grupos</a></li>
          </ul>
        </li>

        <!-- Submenú de Reportes -->
        <li>submenu de reportes
          <ul class="submenu">
            <li><a href="/admin/usuarios">Profesores</a></li> <!-- Vista Usuarios.vue -->
            <li><a href="/admin/reportes/materias">Materias</a></li>
            <li><a href="/admin/reportes/grupos">Grupos</a></li>
          </ul>
        </li>

        <!-- Otras secciones -->
        <li><a href="/admin/estadisticas">Estadísticas</a></li>
        <li><a href="/admin/configuracion">configuración</a></li>
        <li><a href="/bienvenida">Cerrar sesión</a></li>
      </ul>
    </div>
  </div>
</template>

<script setup>
// Script vacío por ahora.
// Aquí se podrá agregar:
// - Manejo de estado para el menú (menuOpen, submenuOpen)
// - Logout con backend
// - Eventos de Vue para interacción dinámica
</script>

<!-- ===============================
     IMPORTACIÓN DE ESTILOS
     - Asegúrate de que los archivos existan en /resources/css/
   =============================== -->
<style src="@/../css/Menu.css"></style>
<style src="@/../css/Marco.css"></style>