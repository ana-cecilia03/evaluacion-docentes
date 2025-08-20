<template>
  <Menu>
    <div class="contenido-periodo">
      <h1 class="titulo">Historial de periodos</h1>
      <div class="table-wrapper">
        <table class="tabla-periodos">
          <thead>
            <tr>
              <th>Número</th>
              <th>Nombre</th>
              <th>Fecha de inicio</th>
              <th>Fecha de término</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="periodo in periodos" :key="periodo.id_periodo">
              <td>{{ periodo.num_periodo }}</td>
              <td>{{ periodo.nombre_periodo }}</td>
              <td>{{ formatFecha(periodo.fecha_inicio) }}</td>
              <td>{{ formatFecha(periodo.fecha_fin) }}</td>
              <td>
                <button
                  :class="periodo.estado === 'activo' ? 'boton-rojo' : 'boton-verde'"
                  @click="cambiarEstado(periodo)"
                >
                  {{ periodo.estado === 'activo' ? 'Desactivar' : 'Activar' }}
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </Menu>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import Menu from '@/layouts/Menu.vue'

const periodos = ref([])

// Cargar los periodos desde el backend
const cargarPeriodos = async () => {
  try {
    const response = await axios.get('/api/periodos')
    periodos.value = response.data
  } catch (error) {
    console.error('Error al cargar los periodos:', error)
  }
}

// Formatear fecha tipo dd/mm/yyyy
const formatFecha = (fecha) => {
  if (!fecha) return ''
  const partes = fecha.split('T')[0].split('-') // yyyy-mm-dd
  return `${partes[2]}/${partes[1]}/${partes[0]}`
}

// Cambiar estado del periodo (activar/inactivar)
const cambiarEstado = async (periodo) => {
  const nuevoEstado = periodo.estado === 'activo' ? 'inactivo' : 'activo'

  try {
    await axios.put(`/api/periodos/${periodo.id_periodo}/estado`, {
      estado: nuevoEstado,
    })

    // Recargar lista para reflejar el cambio
    await cargarPeriodos()
  } catch (error) {
    console.error('Error al cambiar el estado:', error)
  }
}

const crearPeriodo = async () => {
  // Validar que no exista otro periodo activo
  const periodoActivo = periodos.value.find(p => p.estado === 'activo')
  if (periodoActivo) {
    error.value = 'Ya existe un período activo. Debes cerrarlo antes de crear uno nuevo.'
    mensaje.value = ''
    return
  }

  try {
    const response = await axios.post('/api/periodos', {
      num_periodo: form.value.num_periodo,
      nombre_periodo: form.value.nombre,
      fecha_inicio: form.value.inicio,
      fecha_fin: form.value.fin,
      estado: 'activo', // Se crea como activo
    })

    mensaje.value = response.data.message
    error.value = ''
    cancelar()
    await cargarPeriodos() // Recargar la lista
  } catch (err) {
    mensaje.value = ''
    error.value = err.response?.data?.message || 'Error al guardar el período.'
    console.error(err.response?.data)
  }
}


onMounted(() => {
  cargarPeriodos()
})
</script>

<style scoped>
/* Contenedor principal */
.contenido-periodo {
  max-width: 1000px;
  margin: 2rem auto;
  padding: 1.5rem;
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

/* Título */
.titulo {
  font-size: 1.8rem;
  font-weight: 700;
  margin-bottom: 1.5rem;
  text-align: center;
  color: #333;
}

/* Contenedor con scroll para tablas grandes */
.table-wrapper {
  overflow-x: auto;
  border-radius: 8px;
  border: 1px solid #e5e7eb;
}

/* Tabla */
.tabla-periodos {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.95rem;
}

/* Encabezado */
.tabla-periodos thead {
  background-color: #f5f7fa;
}
.tabla-periodos th {
  padding: 0.75rem 1rem;
  text-align: left;
  font-weight: 600;
  color: #444;
  border-bottom: 2px solid #e5e7eb;
}

/* Filas */
.tabla-periodos td {
  padding: 0.75rem 1rem;
  border-bottom: 1px solid #f0f0f0;
  color: #555;
}

.tabla-periodos tr:hover {
  background-color: #fafafa;
}

/* Botones de acción */
.boton-rojo,
.boton-verde {
  padding: 0.4rem 0.9rem;
  font-size: 0.9rem;
  font-weight: 600;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  transition: 0.2s;
}

/* Botón rojo (desactivar) */
.boton-rojo {
  background-color: #e44c5b;
  color: #fff;
}
.boton-rojo:hover {
  background-color: #c62828;
}

/* Botón verde (activar) */
.boton-verde {
  background-color: #156827;
  color: #fff;
}
.boton-verde:hover {
  background-color: #27ae60;
}
</style>


