<template>
  <Menu>
    <div class="contenido-periodo">
      <h1 class="titulo">Configuración</h1>

      <div class="config-box" v-if="!cargando && !error">
        <label>Nombre:</label>
        <input type="text" v-model="form.nombre" :readonly="!editando" />

        <label>Correo electrónico:</label>
        <input type="email" v-model="form.correo" :readonly="!editando" />

        <label>Contraseña (opcional):</label>
        <input
          type="password"
          v-model="form.password"
          :readonly="!editando"
          placeholder="Nueva contraseña (opcional)"
        />
        <input
          type="password"
          v-model="form.password_confirmation"
          :readonly="!editando"
          placeholder="Confirmar nueva contraseña"
        />

        <div class="acciones-config">
          <button class="boton-azul" v-if="!editando" @click="editando = true">Editar</button>
          <div v-else>
            <button class="boton-verde" @click="guardar" :disabled="guardando">Guardar</button>
            <button class="boton-gris" @click="cancelar" :disabled="guardando">Cancelar</button>
          </div>
        </div>
      </div>

      <div v-if="cargando" class="config-box">Cargando tus datos…</div>
      <div v-if="error" class="config-box"><strong>Error:</strong> No se pudieron cargar tus datos.</div>
    </div>
  </Menu>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from '@/lib/axios'
import Menu from '@/layouts/Menu.vue'

const original = ref({ nombre: '', correo: '', rol: '' })
const form = ref({ nombre: '', correo: '', rol: '', password: '', password_confirmation: '' })
const cargando = ref(true)
const error = ref(false)
const editando = ref(false)
const guardando = ref(false)

onMounted(async () => {
  try {
    const { data } = await axios.get('/admin/me')
    original.value = { ...data }
    form.value = { ...data, password: '', password_confirmation: '' }
    cargando.value = false
  } catch (e) {
    console.error('GET /admin/me', e?.response?.status, e?.response?.data || e)
    error.value = true
    cargando.value = false
  }
})

function cancelar() {
  form.value = { ...original.value, password: '', password_confirmation: '' }
  editando.value = false
}

async function guardar() {
  try {
    guardando.value = true
    const payload = {
      nombre: form.value.nombre,
      correo: form.value.correo,
      // Solo manda password si el usuario escribió algo
      ...(form.value.password
        ? { password: form.value.password, password_confirmation: form.value.password_confirmation }
        : {}),
    }
    const { data } = await axios.put('/admin/me', payload)
    original.value = { ...data.user }
    form.value = { ...data.user, password: '', password_confirmation: '' }
    editando.value = false
    alert('✅ Perfil actualizado')
  } catch (e) {
    console.error('PUT /admin/me', e?.response?.status, e?.response?.data || e)
    const msg = e?.response?.data?.message || 'No se pudo actualizar'
    alert('❌' + msg)
  } finally {
    guardando.value = false
  }
}
</script>

