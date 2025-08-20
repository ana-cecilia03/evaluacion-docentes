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
.contenido-periodo :where(table, thead, tbody, tr, th, td) {
  background: transparent;
  color: inherit;
  border: none;
  margin: 0;
  padding: 0;
  font-weight: inherit;
  box-shadow: none;
}

/* Mantén el card y la tabla con esquinas limpias y sin fugas */
.table-wrapper {
  overflow: auto;
  border-radius: 10px;
  border: 1px solid #e5e7eb;
  background: #fff;
}

/* ====== Tabla (reseteada y más legible) ====== */
.tabla-periodos {
  width: 100%;
  border-collapse: collapse;
  table-layout: fixed;
  font-size: 0.96rem;
}

.tabla-periodos thead th,
.tabla-periodos td {
  padding: 0.85rem 1rem;
  border-bottom: 1px solid #eef2f7;
  text-align: left;
  vertical-align: middle;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.tabla-periodos thead th {
  background: #f8fafc;
  color: #1f2937;
  font-weight: 700;
  border-bottom: 2px solid #e5e7eb;
}

/* Zebra + hover suave */
.tabla-periodos tbody tr:nth-child(odd)  { background: #fcfcfd; }
.tabla-periodos tbody tr:hover           { background: #f6f7fb; }

/* Anchos mínimos por columna */
.tabla-periodos th:nth-child(1),
.tabla-periodos td:nth-child(1) { min-width: 100px; } /* Número */
.tabla-periodos th:nth-child(2),
.tabla-periodos td:nth-child(2) { min-width: 260px; } /* Nombre */
.tabla-periodos th:nth-child(3),
.tabla-periodos td:nth-child(3),
.tabla-periodos th:nth-child(4),
.tabla-periodos td:nth-child(4) { min-width: 150px; } /* Fechas */
.tabla-periodos th:nth-child(5),
.tabla-periodos td:nth-child(5) { min-width: 140px; text-align: right; } /* Acciones */

/* Botones */
.boton-rojo,
.boton-verde {
  display: inline-block;
  min-width: 110px;
  padding: 0.5rem 0.9rem;
  border: none;
  border-radius: 8px;
  font-weight: 700;
  line-height: 1;
  box-shadow: 0 1px 2px rgba(0,0,0,.06);
}
.boton-rojo  { background: #e63946; color: #fff; }
.boton-rojo:hover  { background: #c62828; }
.boton-verde { background: #156827; color: #fff; }
.boton-verde:hover { background: #0f5a20; }

/* Título */
.titulo {
  margin: 0 0 1.25rem 0;
  line-height: 1.25;
  letter-spacing: .2px;
}


/* Responsive */
@media (max-width: 1024px) {
  .contenido-periodo { padding: 1rem; }
  .tabla-periodos { font-size: 0.94rem; }
  .tabla-periodos th:nth-child(2),
  .tabla-periodos td:nth-child(2) { min-width: 220px; }
}
@media (max-width: 640px) {
  .tabla-periodos { font-size: 0.92rem; }
  .tabla-periodos thead th, .tabla-periodos td { padding: 0.6rem 0.7rem; }
  .boton-rojo, .boton-verde { min-width: 96px; }
}

</style>



