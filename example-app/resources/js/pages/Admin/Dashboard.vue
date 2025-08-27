<template>
  <Menu>
    <div class="contenedor-cuadro">
      <h1 class="titulo">Crear periodo</h1>

      <form class="formulario-periodo" @submit.prevent="crearPeriodo">
        <div class="campo-formulario">
          <label for="numero">Número:</label>
          <input type="number" id="numero" v-model="form.num_periodo" required />
        </div>

        <div class="campo-formulario">
          <label for="nombre">Nombre:</label>
          <input
            type="text"
            id="nombre"
            v-model="form.nombre"
            placeholder="Ej. Primer Cuatrimestre"
            required
          />
        </div>

        <div class="campo-formulario">
          <label for="inicio">Fecha de inicio:</label>
          <input type="date" id="inicio" v-model="form.inicio" required />
        </div>

        <div class="campo-formulario">
          <label for="fin">Fecha de término:</label>
          <input type="date" id="fin" v-model="form.fin" required />
        </div>

        <div class="botones-formulario">
          <button type="button" class="btn-cancelar" @click="cancelar">Cancelar</button>
          <button type="submit" class="btn-crear">Crear</button>
        </div>

        <p v-if="mensaje" class="mensaje-exito">{{ mensaje }}</p>
        <p v-if="error" class="mensaje-error">{{ error }}</p>
      </form>
    </div>
  </Menu>
</template>

<script setup>
import Menu from '@/layouts/Menu.vue'
import { ref } from 'vue'
import axios from 'axios'

const form = ref({
  num_periodo: '',
  nombre: '',
  inicio: '',
  fin: ''
})

const mensaje = ref('')
const error = ref('')

const crearPeriodo = async () => {
  try {
    const response = await axios.post('/api/periodos', {
      num_periodo: form.value.num_periodo,
      nombre_periodo: form.value.nombre,
      fecha_inicio: form.value.inicio,
      fecha_fin: form.value.fin,
      estado: 'activo' // necesario para validación en backend
    })

    mensaje.value = response.data.message
    error.value = ''
    cancelar()
  } catch (err) {
    mensaje.value = ''
    error.value = err.response?.data?.message || 'Error al guardar el período.'
    console.error(err.response?.data)
  }
}

function cancelar() {
  form.value = { num_periodo: '', nombre: '', inicio: '', fin: '' }
}
</script>

<style scoped>
/* ====== CrearPeriodo (página de formulario) ====== */

/* Caja principal */
.contenedor-cuadro {
  width: 100%;
  max-width: 760px;
  margin: 24px auto;
  padding: 18px 20px;
  background: #ffffff;
  border: 1px solid #e6eef7;
  border-radius: 12px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
}

/* Título */
.titulo {
  margin: 0 0 14px 0;
  font-size: 1.6rem;
  font-weight: 700;
  color: #2b2b2b;
  text-align: center;
}

/* Formulario */
.formulario-periodo {
  display: grid;
  gap: 14px;
}

/* Campo (label + control) */
.campo-formulario {
  display: grid;
  gap: 6px;
}

.campo-formulario label {
  font-weight: 600;
  color: #394455;
  font-size: 0.95rem;
}

/* Inputs y selects */
.campo-formulario input,
.campo-formulario select {
  width: 100%;
  padding: 10px 12px;
  font-size: 0.95rem;
  color: #1f2937;
  background: #ffffff;
  border: 1px solid #cfd8e3;
  border-radius: 10px;
  outline: none;
  transition: border-color 0.15s ease, box-shadow 0.15s ease;
}

.campo-formulario input:focus,
.campo-formulario select:focus {
  border-color: #6aa9ff;
  box-shadow: 0 0 0 3px rgba(106, 169, 255, 0.25);
}

/* Botonera */
.botones-formulario {
  display: flex;
  gap: 10px;
  justify-content: flex-end;
  margin-top: 4px;
}

/* Botones */
.btn-cancelar,
.btn-crear {
  border: none;
  border-radius: 10px;
  padding: 10px 18px;
  font-size: 0.95rem;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 140ms ease, transform 120ms ease, box-shadow 140ms ease;
}

/* Cancelar */
.btn-cancelar {
  background: #aa1a28;
  color: #ffffff;
}
.btn-cancelar:hover { transform: translateY(-1px); }
.btn-cancelar:active { transform: translateY(0); }

/* Crear */
.btn-crear {
  background: #156827;
  color: #ffffff;
  outline: none;
}

/* ← Verde claro al pasar el cursor o al enfocarlo con teclado */
.btn-crear:hover,
.btn-crear:focus-visible {
  background: #3dbe59; /* verde claro */
  transform: translateY(-1px);
}

/* ← Presionado */
.btn-crear:active {
  background: #3dbe59; /* mantener verde claro al click */
  transform: translateY(0);
}

/* Mensajes */
.mensaje-exito,
.mensaje-error {
  margin-top: 6px;
  padding: 10px 12px;
  border-radius: 10px;
  font-size: 0.95rem;
  line-height: 1.25rem;
}

.mensaje-exito {
  background: #e8f7ee;
  color: #106a2f;
  border: 1px solid #bde6ca;
}

.mensaje-error {
  background: #fdecec;
  color: #9e1c1c;
  border: 1px solid #f6c8c8;
}

/* Responsivo */
@media (max-width: 768px) {
  .contenedor-cuadro {
    margin: 12px auto;
    padding: 14px 12px;
  }

  .botones-formulario {
    flex-direction: column-reverse;
    align-items: stretch;
  }

  .btn-cancelar,
  .btn-crear {
    width: 100%;
  }
}
</style>
