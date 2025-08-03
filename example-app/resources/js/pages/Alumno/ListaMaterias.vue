<template>
  <MenuAlumno>
    <main class="contenido-principal">
      <!-- Barra de búsqueda -->
      <div class="actions">
        <input v-model="busqueda" placeholder="Buscar por nombre o materia" />
      </div>

      <!-- Tabla de materias -->
      <table>
        <thead>
            <tr>
              <th>Materia</th>
              <th>Profesor</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="m in materiasFiltradas" :key="m.id_mat_cuatri_car">
              <td>{{ m.materia_nombre }}</td>
              <td>{{ m.profesor_nombre }}</td>
              <td><button class="boton-azul" @click="evaluarMateria(m)">Evaluar</button></td>
            </tr>
            <tr v-if="materiasFiltradas.length === 0">
              <td colspan="3">Sin profesores por evaluar</td>
            </tr>
          </tbody>
      </table>
    </main>
  </MenuAlumno>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import MenuAlumno from '@/layouts/MenuAlumno.vue';
import { useRouter } from 'vue-router';

// Datos de prueba
const materias = ref([
  { 
    id_mat_cuatri_car: 1, 
    materia_nombre: 'Matemáticas', 
    profesor_nombre: 'Ana Torres' 
  },
  { 
    id_mat_cuatri_car: 2, 
    materia_nombre: 'Física', 
    profesor_nombre: 'Carlos Sánchez' 
  },
  { 
    id_mat_cuatri_car: 3, 
    materia_nombre: 'Química', 
    profesor_nombre: 'Fernando Pérez' 
  },
]);

const busqueda = ref('');
const router = useRouter();

// Computed para filtrar materias por nombre o profesor
const materiasFiltradas = computed(() => 
  materias.value.filter(m =>
    m.materia_nombre.toLowerCase().includes(busqueda.value.toLowerCase()) ||
    m.profesor_nombre.toLowerCase().includes(busqueda.value.toLowerCase())
  )
);

// Función para redirigir al alumno a la página de evaluación
function evaluarMateria(m) {
  router.push({ name: 'evaluacion-alumno', params: { id_mat_cuatri_car: m.id_mat_cuatri_car } });
}

// Función para cargar los datos (en este caso no estamos usando axios pero se puede simular la respuesta)
const cargar = async () => {
  // Aquí es donde normalmente harías una llamada a la API
  // const { data } = await axios.get('/api/alumno/materias-por-evaluar');
  // materias.value = data.materias;

  // Pero como estamos en desarrollo, utilizamos los datos de prueba directamente
  materias.value = [
    { id_mat_cuatri_car: 1, materia_nombre: 'Matemáticas', profesor_nombre: 'Ana Torres' },
    { id_mat_cuatri_car: 2, materia_nombre: 'Física', profesor_nombre: 'Carlos Sánchez' },
    { id_mat_cuatri_car: 3, materia_nombre: 'Química', profesor_nombre: 'Fernando Pérez' }
  ];
};

// Llamamos a la función cargar cuando se monta el componente
onMounted(cargar);
</script>

<style src="@/../css/materias.css"></style>
<style src="@/../css/botones.css"></style>
