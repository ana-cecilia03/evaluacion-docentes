<template>
  <MenuAlumno>
    <main class="contenido-principal">
      <!-- Acciones -->
      <div class="actions">
        <input v-model="busqueda" placeholder="Buscar por nombre o materia" />
      </div>

      <!-- Tabla -->
      <table>
        <thead>
          <tr>
            <th>Materia</th>
            <th>Profesor</th>
            <th style="width: 1%"></th>
          </tr>
        </thead>

        <tbody>
          <!-- Estado: cargando -->
          <tr v-if="cargando">
            <td colspan="3">Cargando materias…</td>
          </tr>

          <!-- Estado: error -->
          <tr v-else-if="error">
            <td colspan="3">{{ error }}</td>
          </tr>

          <!-- Sin resultados -->
          <tr v-else-if="materiasFiltradas.length === 0">
            <td colspan="3">Sin profesores por evaluar</td>
          </tr>

          <!-- Lista -->
          <tr v-else v-for="m in materiasFiltradas" :key="m.relacion_id">
            <td>{{ m.materia_nombre }}</td>
            <td>{{ m.profesor_nombre }}</td>
            <td>
              <button
                :class="m.evaluado ? 'boton-gris' : 'boton-azul'"
                :disabled="m.evaluado || enviandoId === m.relacion_id"
                @click="evaluarMateria(m)"
                :aria-disabled="m.evaluado || enviandoId === m.relacion_id"
                :title="m.evaluado ? 'Ya evaluado' : 'Evaluar'"
              >
                <span v-if="enviandoId === m.relacion_id">Enviando…</span>
                <span v-else>{{ m.evaluado ? 'Evaluado' : 'Evaluar' }}</span>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </main>
  </MenuAlumno>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import MenuAlumno from '@/layouts/MenuAlumno.vue' // auto-registrado en <script setup>
import { router } from '@inertiajs/vue3'
import api from '@/lib/axios' // instancia con baseURL '/api'

// -------------------------------------
// State
// -------------------------------------
const materias   = ref([])
const busqueda   = ref('')
const error      = ref(null)
const cargando   = ref(true)
const enviandoId = ref(null)

// -------------------------------------
// Helpers
// -------------------------------------
const goBienvenida = () => router.visit('/bienvenida', { replace: true })

const getAlumnoFromStorage = () => {
  const raw = localStorage.getItem('alumno')
  if (!raw) return null
  try {
    const parsed = JSON.parse(raw)
    // debe tener algún identificador
    if (!parsed || (!parsed.id && !parsed.id_alumno)) return null
    return parsed
  } catch {
    return null
  }
}

const getAlumnoId = (alumno) => alumno?.id_alumno ?? alumno?.id ?? null

// -------------------------------------
// Computed
// -------------------------------------
const materiasFiltradas = computed(() =>
  materias.value.filter(m => {
    const materia = (m.materia_nombre || '').toLowerCase()
    const profe   = (m.profesor_nombre || '').toLowerCase()
    const q       = busqueda.value.toLowerCase()
    return materia.includes(q) || profe.includes(q)
  })
)

// -------------------------------------
// Actions
// -------------------------------------
function evaluarMateria(m) {
  if (!m?.relacion_id) {
    console.error('relacion_id no definido en la materia:', m)
    return
  }
  if (enviandoId.value) return // evita doble click
  enviandoId.value = m.relacion_id

  router.visit(`/alumno/evaluar/${m.relacion_id}`, {
    replace: false,
    onFinish: () => { enviandoId.value = null }
  })
}

// carga (usa endpoint público temporal con ?id=)
const cargar = async () => {
  cargando.value = true
  error.value = null
  try {
    const alumno = getAlumnoFromStorage()
    const id = getAlumnoId(alumno)
    if (!alumno || !id) {
      goBienvenida()
      return
    }

    // /api/alumno/materias-public?id=<id_alumno>
    const { data } = await api.get('/alumno/materias-public', { params: { id } })
    materias.value = data?.materias ?? data?.data ?? []
  } catch (err) {
    const status = err?.response?.status
    if (status === 401 || status === 419 || status === 403) {
      goBienvenida()
      return
    }
    console.error('Error al cargar materias:', {
      status,
      data: err?.response?.data
    })
    error.value = 'Ocurrió un error al cargar las materias'
  } finally {
    cargando.value = false
  }
}

// -------------------------------------
// Guard + Load
// -------------------------------------
onMounted(() => {
  const alumno = getAlumnoFromStorage()
  if (!alumno) {
    goBienvenida()
    return
  }
  cargar()
})
</script>

<style scoped>
/* ===== Contenedor: activa scroll horizontal ===== */
.contenido-principal {
  max-width: 1100px;
  margin: 1.25rem auto;
  padding: 0 1rem;
  overflow-x: auto;                /* <- scroll L-R si no cabe la tabla */
}

/* ===== Reset suave anti-globales dentro del main ===== */
.contenido-principal :where(table, thead, tbody, tr, th, td) {
  background: transparent;
  color: inherit;
  border: none;
  margin: 0;
  padding: 0;
  box-shadow: none;
}

/* ===== Tabla base (se mantiene como tabla también en móvil) ===== */
table {
  width: 100%;
  min-width: 720px;                /* <- asegura scroll en pantallas chicas */
  border-collapse: collapse;
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 6px 18px rgba(0,0,0,.05);
  font-size: .96rem;
}

thead th,
tbody td {
  padding: .8rem 1rem;
  border-bottom: 1px solid #eef2f7;
  vertical-align: middle;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

thead th {
  background: #f8fafc;
  color: #1f2937;
  font-weight: 700;
  text-align: left;
  border-bottom: 2px solid #e5e7eb;
}

tbody tr:nth-child(odd)  { background: #fcfcfd; }
tbody tr:hover           { background: #f6f7fb; }

/* ===== Anchos por columna (evita que colapse la de Acción) ===== */
thead th:nth-child(1), tbody td:nth-child(1) { min-width: 260px; } /* Materia  */
thead th:nth-child(2), tbody td:nth-child(2) { min-width: 260px; } /* Profesor */
thead th:last-child,  tbody td:last-child    { 
  width: 160px !important;
  min-width: 160px;
  text-align: right;
}
tbody td:last-child {
  overflow: visible;
  white-space: normal;
}

/* ===== Botones locales (ganan a globales) ===== */
.boton-azul,
.boton-gris {
  display: inline-block;
  min-width: 110px;
  padding: .5rem .9rem;
  border: none;
  border-radius: 999px;
  font-weight: 700;
  line-height: 1;
  cursor: pointer;
  transition: filter .15s ease;
  text-align: center;
  box-shadow: 0 1px 2px rgba(0,0,0,.06);
}
.boton-azul { background: #130968; color: #fff; }
.boton-azul:hover { filter: brightness(0.95); }
.boton-gris { background: #9ca3af; color: #111827; }
.boton-gris[disabled],
.boton-azul[disabled] { opacity: .7; cursor: not-allowed; }

/* ===== Ajustes en móvil (mantiene scroll y botón visible) ===== */
@media (max-width: 720px) {
  .contenido-principal { padding: 0 .5rem; }
  table { font-size: .94rem; min-width: 720px; }   /* fuerza scroll L-R */
  tbody td:last-child button { width: 100%; max-width: 200px; }
}
/* ——— Más interlineado/aire en celdas ——— */
thead th,
tbody td {
  line-height: 1.55;          /* antes ~1.2–1.3 */
  padding-top: 1rem;          /* +espacio arriba */
  padding-bottom: 1rem;       /* +espacio abajo */
}

/* Un pelín más de aire en las columnas de texto */
tbody td:nth-child(1),
tbody td:nth-child(2) {
  line-height: 1.6;
}

/* En móvil, mantenlo cómodo */
@media (max-width: 720px) {
  thead th,
  tbody td {
    line-height: 5.0;
    padding-top: 1.05rem;
    padding-bottom: 1.05rem;
  }

  /* por si el botón queda muy pegado al texto */
  tbody td:last-child button {
    margin-top: .25rem;
  }
}
/* --- Desktop / wide: más ancho y mismo interlineado cómodo --- */
@media (min-width: 1024px) {
  .contenido-principal {
    max-width: 1280px;       /* antes 1100px */
  }
  thead th,
  tbody td {
    line-height: 1.6 !important;
    padding-top: 1.05rem !important;
    padding-bottom: 1.05rem !important;
  }
  /* un poco más de espacio para el botón */
  thead th:last-child,
  tbody td:last-child {
    width: 180px !important;
    min-width: 180px;
  }
}

/* Ultra-wide (opc.): respira aún más sin perder centrado */
@media (min-width: 1440px) {
  .contenido-principal { max-width: 92vw; }
  thead th:nth-child(1), tbody td:nth-child(1) { min-width: 320px; }
  thead th:nth-child(2), tbody td:nth-child(2) { min-width: 320px; }
  thead th:last-child,  tbody td:last-child    { width: 200px !important; min-width: 200px; }
}

/* Alineación clara por columna (vence a globales que centran) */
table thead th:not(:last-child),
table tbody td:not(:last-child) { text-align: left !important; }
table thead th:last-child,
table tbody td:last-child { text-align: right !important; }

/* Móvil: corrige interlineado si tenías 5.0 y lo deja cómodo */
@media (max-width: 720px) {
  thead th,
  tbody td {
    line-height: 1.55 !important;
    padding-top: 0.95rem !important;
    padding-bottom: 0.95rem !important;
  }
}
</style>

