<template>
  <div class="page-admin" @click="handleOutsideClick">
    <!-- ENCABEZADO AZUL -->
    <header class="header-admin">
      <a href="/admin/dashboard">
        <img src="/img/logo.png" alt="Logo UPTex" class="logo">
      </a>
      <!-- Menú hamburguesa solo en móviles -->
      <div class="acciones-header">
      <img src="/img/salir.png" class="salir-icono" alt="Salir" @click="cerrarSesion" />
      <div class="hamburger" @click="toggleMenu">
        <div class="bar"></div>
        <div class="bar"></div>
        <div class="bar"></div>
      </div>
    </div>
    </header>
    

    <!-- PANEL DE CONTROL LATERAL (MENÚ) -->
    <aside :class="['menu-lateral', { abierto: menuAbierto }]" ref="menuLateral">

      <div class="panel">
        <ul>
          <li><a href="/admin/dashboard"><img src="/img/inicio.png" /> Dashboard</a></li>
          <li><a href="/admin/periodo"><img src="/img/periodo.png" /> Periodo</a></li>
          <li class="seccion">Registrar</li>
          <li><a href="/registro/carrera"><img src="/img/registros.png" /> Carreras</a></li>
          <li><a href="/registro/alumnos"><img src="/img/alumno.png" /> Alumnos</a></li>
          <li><a href="/registro/profesores"><img src="/img/profesor.png" /> Profesores</a></li>
          <li><a href="/registro/grupo"><img src="/img/grupos.png" /> Grupos</a></li>
          <li><a href="/registro/asignaturas"><img src="/img/asignatura.png" /> Materias</a></li>

          <li class="seccion">Reportes</li>
          <li><a href="/reportes/alumnos"><img src="/img/repasig.png" /> Materias</a></li>
          <li><a href="/reportes/profesores"><img src="/img/reprof.png" /> Profesores</a></li>
          <li><a href="/reportes/grupos"><img src="/img/repgrup.png" /> Grupos</a></li>

          <li class="seccion">Relación</li>
          <li><a href="#"><img src="/img/Alum-Prof.png" /> Alumno-Prof</a></li>
          <li><a href="#"><img src="/img/Mat-Per-Carr.png" /> Mat-Per-Carr</a></li>

          <li class="seccion">Extras</li>
          <li><a href="/estadisticas"><img src="/img/estadisticas.png" /> Estadísticas</a></li>
          <li><a href="/admin/configuracion"><img src="/img/confi.png" /> Configuración</a></li>
          <li><a href="/inicio"><img src="/img/salir.png" @click="cerrarSesion"/> Cerrar Sesion</a></li>
        </ul>
      </div>
    </aside>

    <!-- CONTENIDO PRINCIPAL -->
    <main class="contenido-principal">
      <slot />
    </main>

    <!-- PIE DE PÁGINA AZUL -->
    <footer class="footer-admin">
      Sistema de encuestas © UNIVERSIDAD POLITÉCNICA DE TEXCOCO
    </footer>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'

const menuAbierto = ref(false)
const menuLateral = ref(null)

const toggleMenu = () => {
  menuAbierto.value = !menuAbierto.value
}

// Cierra el menú si se hace clic fuera de él
const handleOutsideClick = (event) => {
  if (menuAbierto.value && menuLateral.value && !menuLateral.value.contains(event.target) && !event.target.closest('.hamburger')) {
    menuAbierto.value = false
  }
}

const cerrarSesion = () => {
  window.location.href = '/bienvenida' // o la ruta que manejes para cerrar sesión
}

// Añadimos un listener para el clic fuera del menú cuando el componente se monta
onMounted(() => {
  document.addEventListener('click', handleOutsideClick)
})

// Removemos el listener cuando el componente se destruye
onBeforeUnmount(() => {
  document.removeEventListener('click', handleOutsideClick)
})

</script>

<style src="@/../css/Marco.css"></style>
