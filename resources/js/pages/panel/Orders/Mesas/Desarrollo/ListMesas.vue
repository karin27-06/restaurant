<script setup> 
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';
import { debounce } from 'lodash';
import { useToast } from 'primevue/usetoast';
import Toolbar from 'primevue/toolbar';
import Dropdown from 'primevue/dropdown';
import InputText from 'primevue/inputtext';



// Initialize toast
const toast = useToast();

const mesas = ref([]);
const searchQuery = ref('');
const selectedFloor = ref(null); // null significa 'Todos los pisos'
const selectedArea = ref(null); // null significa 'Todas las áreas'
const floors = ref([]);  // Array para almacenar los pisos
const areas = ref([]);    // Array para almacenar las áreas
import Button from 'primevue/button';
import { router } from '@inertiajs/core';

// Función para cargar las mesas con los filtros
const loadMesas = async () => {
  try {
    let url = '/mesa'; // URL base de la API

    // Agregar parámetros de filtro a la URL si se seleccionan valores diferentes de "Todos"
    const params = [];
    if (selectedFloor.value && selectedFloor.value !== 'all') {
      params.push(`idFloor=${selectedFloor.value}`);
    }
    if (selectedArea.value && selectedArea.value !== 'all') {
      params.push(`idArea=${selectedArea.value}`);
    }
    if (searchQuery.value) {
      params.push(`search=${searchQuery.value}`); // Filtro por número de mesa
    }
    if (params.length > 0) {
      url += `?${params.join('&')}`;
    }

    // Realizar la solicitud GET con los parámetros adecuados
    const response = await axios.get(url);
    mesas.value = response.data.data;

  } catch (error) {
    console.error('Error cargando mesas:', error);
  }
};

onMounted(async () => {
  try {
    const response = await axios.get('/mesa');
    mesas.value = response.data.data;
    
    // Obtener los pisos únicos basados en idFloor y ordenar de menor a mayor
    floors.value = [{ label: 'Todos los pisos', value: 'all' }, ...mesas.value.reduce((uniqueFloors, mesa) => {
      if (!uniqueFloors.some(floor => floor.value === mesa.idFloor)) {
        uniqueFloors.push({ label: mesa.floor_name, value: mesa.idFloor });
      }
      return uniqueFloors;
    }, [])];

    // Ordenar los pisos por idFloor de menor a mayor
    floors.value.sort((a, b) => a.value - b.value);

    // Obtener las áreas únicas basadas en idArea y ordenar de menor a mayor
    areas.value = [{ label: 'Todas las áreas', value: 'all' }, ...mesas.value.reduce((uniqueAreas, mesa) => {
      if (!uniqueAreas.some(area => area.value === mesa.idArea)) {
        uniqueAreas.push({ label: mesa.area_name, value: mesa.idArea });
      }
      return uniqueAreas;
    }, [])];

    // Ordenar las áreas por idArea de menor a mayor
    areas.value.sort((a, b) => a.value - b.value);

  } catch (error) {
    console.error('Error cargando mesas:', error);
  }
});

// Watch para detectar cambios en los filtros (piso, área y búsqueda)
watch([selectedFloor, selectedArea, searchQuery], () => {
  loadMesas(); // Recargar las mesas cada vez que se selecciona un filtro o se actualiza la búsqueda
});


const goBack = () => {
    const url = `/ordenes/`;
    router.visit(url);
};
</script>

<template>
 <div>
<Toolbar class="mb-4">
  <template #start>
       <Button label="Volver" icon="pi pi-chevron-left" severity="secondary" class="mr-2" @click="goBack" />

  </template>

  <template #end>
  
   <div class="flex space-x-4 ml-auto">
      <!-- Dropdown for Floor -->
      <Dropdown v-model="selectedFloor" :options="floors" option-label="label" option-value="value" placeholder="Selecciona un Piso" class="w-1/1" />
      
      <!-- Dropdown for Area -->
      <Dropdown v-model="selectedArea" :options="areas" option-label="label" option-value="value" placeholder="Selecciona una Area" class="w-1/1" />
      
      <!-- Search Input -->
      <InputText v-model="searchQuery" debounce="300" placeholder="Buscar por numero" class="w-1/3" />
    </div>
  </template>
</Toolbar>
<div class="grid grid-cols-12 gap-4">
  <!-- Repeat for each mesa -->
  <div v-for="mesa in mesas" :key="mesa.id" class="col-span-12 lg:col-span-6 xl:col-span-3">
    <div class="card mb-0">
      <div class="flex justify-between mb-4">
        <div>
          <span class="block text-muted-color font-medium mb-4">Mesa Nº</span>
          <div class="text-surface-900 dark:text-surface-0 font-medium text-xl">{{ mesa.tablenum }}</div>
        </div>
        <div class="flex items-center justify-center bg-blue-100 dark:bg-blue-400/10 rounded-border" style="width:2.5rem;height:2.5rem;">
          <i class="pi pi-plus text-blue-500 !text-xl"></i>
        </div>
      </div>
      <div class="flex space-x-4">
        <span class="text-muted-color">{{ mesa.floor_name }} - {{ mesa.area_name }} - {{ mesa.capacity }} personas</span>
      </div>
    </div>
  </div>
</div>
</div>
</template>