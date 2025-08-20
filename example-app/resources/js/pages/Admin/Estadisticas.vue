<template>
  <Menu>
    <main class="contenido-principal">
      <header class="pf-header">
        <h1>Puntaje final — todos los profesores</h1>
      </header>

      <p v-if="error" class="pf-error">{{ error }}</p>
      <p v-else-if="isLoading">Cargando...</p>

      <section v-else class="pf-card">
        <table class="pf-table">
          <thead>
            <tr>
              <th style="min-width:240px">Profesor</th>
              <th>Promedio alumnos</th>
              <th>Calificación admin (0–10)</th>
              <th>Total</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="r in filas" :key="r.profesor_id">
              <td>{{ r.profesor || ('#' + r.profesor_id) }}</td>

              <td>
                <div class="pf-bar-track">
                  <div class="pf-bar-fill alumnos" :style="{ width: w(r.promedio_alumnos) }"></div>
                </div>
                <small v-if="r.total === null" class="pf-badge-sd">S/D</small>
                <small v-else>{{ f(r.total) }}</small>
              </td>

              <td>
                <div class="pf-bar-track">
                  <div class="pf-bar-fill admin" :style="{ width: w(r.calificacion_admin) }"></div>
                </div>
                <small v-if="r.total === null" class="pf-badge-sd">S/D</small>
                <small v-else>{{ f(r.total) }}</small>
              </td>

              <td>
                <div class="pf-bar-track">
                  <div class="pf-bar-fill total" :style="{ width: w(r.total) }"></div>
                </div>
                <small v-if="r.total === null" class="pf-badge-sd">S/D</small>
                <small v-else>{{ f(r.total) }}</small>
              </td>
            </tr>
          </tbody>
        </table>
      </section>
    </main>
  </Menu>
</template>

<script setup>
import Menu from '@/layouts/Menu.vue'
import { ref, onMounted } from 'vue'

const filas = ref([])      // [{ profesor_id, profesor, promedio_alumnos(0–10), calificacion_admin(0–10), total(0–10) }]
const isLoading = ref(false)
const error = ref('')

onMounted(async () => {
  isLoading.value = true
  try {
    const token = localStorage.getItem('adminToken') || localStorage.getItem('token') || ''
    const res = await fetch('/api/admin/reportes/puntaje-final', {
      headers: {
        Accept: 'application/json',
        ...(token ? { Authorization: `Bearer ${token}` } : {})
      },
      credentials: 'include'
    })
    if (!res.ok) throw new Error(`Error ${res.status}: ${res.statusText}`)
    const data = await res.json()

    const arr = Array.isArray(data) ? data : []

    filas.value = arr.map(r => {
      // Normalizamos todo a 0–10 y redondeamos a 2 decimales, por si acaso.
      const alum10  = clamp10(toNum(r.promedio_alumnos))
      // si por error viniera en 1–5, la heurística lo escala
      const rawAdmin = toNum(r.calificacion_admin)
      const admin10  = clamp10(
        rawAdmin === null ? null : (rawAdmin <= 5 ? rawAdmin * 2 : rawAdmin)
      )
      const total    = (alum10 !== null && admin10 !== null) ? clamp10((alum10 + admin10) / 2) : null

      return {
        profesor_id: r.profesor_id,
        profesor: r.profesor ?? r.nombre_completo ?? null,
        promedio_alumnos: alum10,
        calificacion_admin: admin10,
        total
      }
    })
  } catch (e) {
    error.value = e?.message || 'No se pudo cargar el listado.'
  } finally {
    isLoading.value = false
  }
})

function toNum(v) {
  if (v === null || v === undefined || v === '') return null
  const n = Number(v)
  return Number.isNaN(n) ? null : n
}
function clamp10(n) {
  if (n === null || n === undefined || Number.isNaN(Number(n))) return null
  const x = Number(n)
  const clamped = Math.max(0, Math.min(10, x))
  return Number(clamped.toFixed(2))
}
function f(n) {
  if (n === null || n === undefined || Number.isNaN(Number(n))) return 'S/D'
  return clamp10(n).toFixed(2)
}
function w(n) {
  const val = clamp10(n)
  if (val === null) return '0%'
  const pct = (val / 10) * 100
  return `${pct}%`
}
</script>
<style scoped>
/* 1) Encabezado sticky + sombra al hacer scroll */
.pf-card {
  position: relative;
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  box-shadow: 0 6px 20px rgba(0,0,0,.06);
  padding: 0;
  overflow: auto; /* importante para sticky */
  max-height: 72vh; /* opcional: limita alto si hay muchos registros */
}
.pf-table {
  width: 100%;
  border-collapse: collapse;
  table-layout: fixed;
  font-size: 0.95rem;
}
.pf-table thead th {
  position: sticky;
  top: 0;
  z-index: 1;
  background: #f8fafc;
  color: #1f2937;
  font-weight: 700;
  border-bottom: 2px solid #e5e7eb;
  padding: 0.75rem 1rem;
}
.pf-card::after { /* sombra bajo header cuando hay scroll */
  content: "";
  position: sticky;
  top: 48px; /* aprox alto del thead */
  display: block;
  height: 0;
  box-shadow: 0 8px 10px -8px rgba(0,0,0,.12);
}

/* 2) Zebra + celdas */
.pf-table tbody tr:nth-child(odd) { background: #fcfcfd; }
.pf-table td {
  padding: 0.75rem 1rem;
  border-bottom: 1px solid #eef2f7;
  vertical-align: middle;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

/* 3) Números: derecha + cifras tabulares */
.pf-table td,
.pf-table th {
  font-variant-numeric: tabular-nums; /* columnas perfectamente alineadas */
}
.pf-table td:nth-child(2),
.pf-table td:nth-child(3),
.pf-table td:nth-child(4) { text-align: right; }

/* 4) Badge S/D */
.pf-badge-sd {
  display: inline-block;
  padding: 2px 8px;
  border-radius: 999px;
  background: #eef2f7;
  color: #4b5563;
  font-size: 0.8rem;
  font-weight: 600;
}

/* 5) Barras (mantén tus clases) con mejor contraste */
.pf-bar-track {
  position: relative;
  width: 100%;
  height: 8px;
  background: #e5e7eb;
  border-radius: 999px;
  overflow: hidden;
  margin-bottom: .35rem;
}
.pf-bar-fill { height: 100%; width: 0%; border-radius: inherit; transition: width .35s ease; }
.pf-bar-fill.alumnos { background: #2563eb; } /* +contraste */
.pf-bar-fill.admin   { background: #d97706; }
.pf-bar-fill.total   { background: #059669; }

/* 6) Responsive mínimo */
@media (max-width: 720px) {
  .pf-table { font-size: .92rem; }
  .pf-table th, .pf-table td { padding: 0.6rem 0.7rem; }
  .pf-bar-track { height: 7px; }
}

/* 7) Reseteos anti-globales agresivos (actívalos si notas conflictos) */

.pf-table th, .pf-table td {
  background: transparent !important;
  border-bottom: 1px solid #eef2f7 !important;
  padding: 0.75rem 1rem !important;
  color: #374151 !important;
}

/* ——— Vista a lo ANCHO ——— */
.contenido-principal {
  max-width: 100%;     /* ocupar todo el ancho */
  width: 100%;         
  padding-left: 2rem;  /* margen interno opcional */
  padding-right: 2rem;
}

.pf-card {
  width: 100%;         /* estirarse en horizontal */
  max-width: 100%;
}
</style>

