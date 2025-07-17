<template>
  <header v-if="!pie" class="header">
    <img src="/img/logo.png" alt="Logo UPTex" />
    <button class="menu-btn" @click="toggleMenu">
      <i class="fas fa-bars"></i>
    </button>

    <nav :class="['nav', { open: menuAbierto }]">
      <a href="/inicio">Inicio</a>
      <a href="/contacto">Contáctenos</a>
      <a href="/apps">Apps</a>
      <a href="/bienvenida">Iniciar sesión</a>
    </nav>
  </header>

  <footer v-else class="footer">
    Sistema de encuestas © UNIVERSIDAD POLITÉCNICA DE TEXCOCO
    <div class="social">
        <a href="https://x.com/uptexcoco"  aria-label="Twitter">
            <i class="fab fa-twitter"></i>
        </a>
        <a href="https://www.facebook.com/UPTexcoco/"  aria-label="Facebook">
            <i class="fab fa-facebook"></i>
        </a>
    </div>
  </footer>
</template>

<script setup>
import { ref } from 'vue'

const menuAbierto = ref(false)

function toggleMenu() {
  menuAbierto.value = !menuAbierto.value
}

defineProps({
  pie: {
    type: Boolean,
    default: false,
  },
})
</script>

<style scoped>
.header {
  background-color: #6b9edc;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 10px 30px;
  flex-wrap: wrap;
}

.header img {
  height: 50px;
}

.menu-btn {
  display: none;
  background: none;
  border: none;
  font-size: 26px;
  color: white;
  cursor: pointer;
}

.nav {
  display: flex;
  gap: 20px;
}

.nav a {
  color: white;
  font-weight: bold;
  text-decoration: none;
}

.footer {
  background-color: #6b9edc;
  color: white;
  text-align: center;
  padding: 10px;
  font-size: 14px;
}

.social {
  margin-top: 5px;
}

.social i {
  font-size: 28px; /* antes 20px */
  margin: 0 14px;
  color: white;
  transition: transform 0.3s, color 0.3s;
}

.social a:hover i {
  color: #000661;
  transform: scale(1.2);
}

/* Responsive */
@media (max-width: 768px) {
  .menu-btn {
    display: block;
  }

  .nav {
    flex-direction: column;
    width: 100%;
    display: none;
    margin-top: 10px;
  }

  .nav.open {
    display: flex;
  }
}
</style>