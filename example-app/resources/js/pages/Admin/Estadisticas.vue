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
                <small>{{ f(r.promedio_alumnos) }}</small>
              </td>

              <td>
                <div class="pf-bar-track">
                  <div class="pf-bar-fill admin" :style="{ width: w(r.calificacion_admin) }"></div>
                </div>
                <small>{{ f(r.calificacion_admin) }}</small>
              </td>

              <td>
                <div class="pf-bar-track">
                  <div class="pf-bar-fill total" :style="{ width: w(r.total) }"></div>
                </div>
                <small>{{ r.total === null ? 'S/D' : f(r.total) }}</small>
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

