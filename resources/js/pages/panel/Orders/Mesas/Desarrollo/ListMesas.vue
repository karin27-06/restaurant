<script setup>
import { router } from '@inertiajs/core';
import axios from 'axios';
import Button from 'primevue/button';
import Dialog from 'primevue/dialog';
import Dropdown from 'primevue/dropdown';
import InputText from 'primevue/inputtext';
import Toolbar from 'primevue/toolbar';
import { useToast } from 'primevue/usetoast';
import { computed, onMounted, ref, watch } from 'vue';

// Initialize toast
const toast = useToast();
const showInsumosDialog = ref(false); // Para mostrar el dialogo de insumos

const mesas = ref([]);
const searchQuery = ref('');
const selectedFloor = ref(null); // null significa 'Todos los pisos'
const selectedArea = ref(null); // null significa 'Todas las áreas'
const floors = ref([]); // Array para almacenar los pisos
const areas = ref([]); // Array para almacenar las áreas
const showOrderForm = ref(false);
const selectedMesa = ref(null);
const platos = ref([]);

const order = ref({
    mesaId: null,
    tablenum: null,
    platos: [],
});
const selectedPlato = ref(null); // Para almacenar el plato seleccionado

const showReciboToolbar = ref(false);
// Variables de estado para la paginación
const currentPage = ref(1); // Página actual
const perPage = ref(7); // Platos por página
const totalPages = ref(1); // Total de páginas

// Función para cargar los platos desde la API con búsqueda y paginación
const loadPlatos = async (search = '') => {
    try {
        // Construir la URL con el parámetro de búsqueda y paginación
        const response = await axios.get(`/plato?search=${search}&page=${currentPage.value}&per_page=${perPage.value}`);
        platos.value = response.data.data;
        totalPages.value = response.data.meta.last_page; // Obtener el total de páginas
    } catch (error) {
        console.error('Error cargando platos:', error);
    }
};

const showOrderFormForMesa = (mesaId, tablenum) => {
    console.log('Mesa ID:', mesaId);
    console.log('Tablenum:', tablenum);
    selectedMesa.value = mesas.value.find((mesa) => mesa.id === mesaId);
    order.value.mesaId = selectedMesa.value.id;
    order.value.tablenum = tablenum;
    tableNumber.value = tablenum; // Asigna el valor de tablenum a tableNumber
    showOrderForm.value = true;

    // Al seleccionar una mesa, muestra el toolbar para generar recibo
    showReciboToolbar.value = true;
    loadPlatos();
};

// Función para agregar un plato al pedido
const agregarAlPedido = (plato) => {
    // Verifica si el plato ya está en el pedido
    const platoExistente = order.value.platos.find((pedido) => pedido.id === plato.id);

    if (platoExistente) {
        // Si ya existe, solo actualiza la cantidad
        platoExistente.cantidad = Math.min(platoExistente.cantidad + 1, plato.quantity);
    } else {
        // Si no existe, agregarlo al pedido con cantidad 1
        order.value.platos.push({ ...plato, cantidad: 1 });
    }
};

// Función para mostrar los insumos de un plato en el Dialog
const verInsumos = (plato) => {
    console.log(plato); // Verifica qué tiene `plato`
    selectedPlato.value = plato;
    showInsumosDialog.value = true;
};

// Función para cancelar la visualización de insumos (cerrar el dialogo)
const cancelarInsumos = () => {
    showInsumosDialog.value = false; // Cerrar el dialogo
};
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

        // Filtrar áreas según el piso seleccionado
        updateAreas();
    } catch (error) {
        console.error('Error cargando mesas:', error);
    }
};

// Función para actualizar las áreas basadas en el piso seleccionado
const updateAreas = () => {
    if (selectedFloor.value === 'all' || !selectedFloor.value) {
        areas.value = [
            { label: 'Todas las áreas', value: 'all' },
            ...mesas.value.reduce((uniqueAreas, mesa) => {
                if (!uniqueAreas.some((area) => area.value === mesa.idArea)) {
                    uniqueAreas.push({ label: mesa.area_name, value: mesa.idArea });
                }
                return uniqueAreas;
            }, []),
        ];
    } else {
        areas.value = [
            { label: 'Todas las áreas', value: 'all' },
            ...mesas.value
                .filter((mesa) => mesa.idFloor === selectedFloor.value)
                .reduce((uniqueAreas, mesa) => {
                    if (!uniqueAreas.some((area) => area.value === mesa.idArea)) {
                        uniqueAreas.push({ label: mesa.area_name, value: mesa.idArea });
                    }
                    return uniqueAreas;
                }, []),
        ];
    }

    // Ordenar las áreas por idArea de menor a mayor
    areas.value.sort((a, b) => a.value - b.value);
};

onMounted(async () => {
    try {
        const response = await axios.get('/mesa');
        mesas.value = response.data.data;

        // Obtener los pisos únicos basados en idFloor y ordenar de menor a mayor
        floors.value = [
            { label: 'Todos los pisos', value: 'all' },
            ...mesas.value.reduce((uniqueFloors, mesa) => {
                if (!uniqueFloors.some((floor) => floor.value === mesa.idFloor)) {
                    uniqueFloors.push({ label: mesa.floor_name, value: mesa.idFloor });
                }
                return uniqueFloors;
            }, []),
        ];

        // Ordenar los pisos por idFloor de menor a mayor
        floors.value.sort((a, b) => a.value - b.value);

        // Actualizar áreas después de cargar las mesas
        updateAreas();
    } catch (error) {
        console.error('Error cargando mesas:', error);
    }
});
// Variable para almacenar el texto de búsqueda
const searchQueryPlato = ref('');
// Watch para detectar cambios en los filtros (piso, área y búsqueda)
watch([selectedFloor, selectedArea, searchQuery], () => {
    loadMesas(); // Recargar las mesas cada vez que se selecciona un filtro o se actualiza la búsqueda
});
// Watch para detectar cambios en el texto de búsqueda y cargar platos con la paginación
watch(searchQueryPlato, (newSearchQuery) => {
    currentPage.value = 1; // Reiniciar la página a la primera cuando se cambie la búsqueda
    loadPlatos(newSearchQuery);
});

// Watch para detectar cambios en la página actual
watch(currentPage, () => {
    loadPlatos(searchQueryPlato.value); // Recargar platos al cambiar de página
});
// Watch para cambiar de piso y resetear área seleccionada
watch(selectedFloor, () => {
    selectedArea.value = null; // Resetear área seleccionada
    updateAreas(); // Actualizar áreas
});

const goBack = () => {
    const url = `/ordenes/`;
    router.visit(url);
};

const goBackOrder = () => {
    // Limpiar el pedido (vaciar platos y otros datos)
    order.value.platos = [];
    order.value.mesaId = null;
    order.value.tablenum = null;

    // Ocultar el formulario de pedido y volver a mostrar las mesas
    showOrderForm.value = false;
    showReciboToolbar.value = false;

    // Mostrar un mensaje de confirmación
    toast.add({
        severity: 'info',
        summary: 'Pedido cancelado',
        detail: 'Has salido del pedido y se ha borrado toda la información.',
        life: 3000,
    });
};

const tableNumber = ref(1);
const isPlatoAgregado = (plato) => {
    return order.value.platos.some((pedido) => pedido.id === plato.id);
};
// Función para ajustar la cantidad del plato
const adjustQuantity = (plato, increment) => {
    // Si la cantidad es 1 y se intenta restar, eliminar el plato del pedido
    if (plato.cantidad === 1 && increment === -1) {
        eliminarDelPedido(order.value.platos.indexOf(plato)); // Elimina el plato
    } else {
        // Actualiza la cantidad, asegurándose de no exceder el stock
        plato.cantidad = Math.min(Math.max(plato.cantidad + increment, 1), plato.quantity);
    }
};

// Computed property para filtrar los platos según el texto de búsqueda
const filteredPlatos = computed(() => {
    if (!searchQueryPlato.value) {
        return platos.value; // Si no hay búsqueda, mostrar todos los platos
    }
    return platos.value.filter((plato) => plato.name.toLowerCase().includes(searchQueryPlato.value.toLowerCase()));
});
// Función para eliminar un plato del pedido
const eliminarDelPedido = (index) => {
    order.value.platos.splice(index, 1); // Elimina el plato del array
};
// Función para capitalizar la primera letra de un string
const capitalizeFirstLetter = (str) => {
    if (!str) return ''; // Retorna vacío si no hay texto
    return str.charAt(0).toUpperCase() + str.slice(1).toLowerCase();
};

const cancelOrder = () => {
    // Limpiar el pedido (vaciar platos y otros datos)
    order.value.platos = [];
    order.value.mesaId = null;
    order.value.tablenum = null;

    // Ocultar el formulario de pedido y regresar a la vista de mesas
    showOrderForm.value = false;
    showReciboToolbar.value = false;

    // Mostrar un mensaje de confirmación
    toast.add({
        severity: 'info',
        summary: 'Pedido cancelado',
        detail: 'Has salido del pedido y se ha borrado toda la información.',
        life: 3000,
    });
};
const realizarPedido = () => {
    // Verificar si hay platos seleccionados
    if (order.value.platos.length === 0) {
        // Si no hay platos, mostrar un toast
        toast.add({
            severity: 'warn',
            summary: 'No hay platos seleccionados',
            detail: 'Por favor, selecciona al menos un plato para realizar el pedido.',
            life: 3000,
        });
    } else {
        // Si hay platos, proceder con la lógica para realizar el pedido
        console.log('Pedido realizado con éxito!');
        // Aquí iría la lógica para enviar el pedido a la API o realizar otra acción
    }
};
</script>

<template>
    <div>
        <Toolbar v-if="showReciboToolbar" class="mb-4">
            <template #start>
                <!-- Cambio el label a "Salir del Pedido" -->
                <Button label="Salir del Pedido" icon="pi pi-times" severity="secondary" class="mr-2" @click="goBackOrder" />
            </template>
            <template #end>
                <Button label="Generar Recibo" icon="pi pi-file" severity="primary" class="mr-2" />
            </template>
        </Toolbar>

        <Toolbar v-if="!showReciboToolbar" class="mb-4">
            <template #start>
                <Button label="Volver" icon="pi pi-chevron-left" severity="secondary" class="mr-2" @click="goBack" />
            </template>

            <template #end>
                <div class="ml-auto flex space-x-4">
                    <!-- Dropdown for Floor -->
                    <Dropdown
                        v-model="selectedFloor"
                        :options="floors"
                        option-label="label"
                        option-value="value"
                        placeholder="Selecciona un Piso"
                        class="w-1/1"
                    />

                    <!-- Dropdown for Area -->
                    <Dropdown
                        v-model="selectedArea"
                        :options="areas"
                        option-label="label"
                        option-value="value"
                        placeholder="Selecciona una Area"
                        class="w-1/1"
                    />

                    <!-- Search Input -->
                    <InputText v-model="searchQuery" debounce="300" placeholder="Buscar por numero" class="w-1/3" />
                </div>
            </template>
        </Toolbar>

        <!-- Section to display mesas -->
        <div v-if="!showOrderForm" class="grid grid-cols-12 gap-4">
            <div v-for="mesa in mesas" :key="mesa.id" class="col-span-12 lg:col-span-6 xl:col-span-3">
                <div class="card mb-0">
                    <div class="mb-4 flex justify-between">
                        <div>
                            <span class="mb-4 block font-medium text-muted-color">Mesa Nº</span>
                            <div class="text-xl font-medium text-surface-900 dark:text-surface-0">{{ mesa.tablenum }}</div>
                        </div>
                        <div
                            class="flex cursor-pointer items-center justify-center bg-blue-100 rounded-border dark:bg-blue-400/10"
                            style="width: 2.5rem; height: 2.5rem"
                            @click="showOrderFormForMesa(mesa.id, mesa.tablenum)"
                        >
                            <i class="pi pi-plus !text-xl text-blue-500"></i>
                        </div>
                    </div>
                    <div class="flex space-x-4">
                        <span class="text-muted-color">{{ mesa.floor_name }} - {{ mesa.area_name }} - {{ mesa.capacity }} personas</span>
                    </div>
                </div>
            </div>
        </div>

        <div>
            <div v-if="showOrderForm" class="card flex flex-col">
                <div class="grid flex-grow grid-cols-2 gap-6">
                    <!-- Columna 1: Mesa y platos disponibles -->
                    <div class="col-span-1 border-r pr-4">
                        <h2 class="mb-4 text-lg font-semibold uppercase">
                            <strong>Mesa Nº {{ order.tablenum || 'Cargando...' }}</strong>
                        </h2>

                        <!-- Buscador de platos -->
                        <InputText v-model="searchQueryPlato" placeholder="Buscar plato..." class="mb-4 w-full" />

                        <!-- Mostrar platos disponibles con búsqueda -->
                        <div class="platos-disponibles">
                            <div v-for="plato in filteredPlatos" :key="plato.id" class="plato-item mb-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <!-- Nombre del plato y precio con texto un poco más pequeño -->
                                        <span class="text-lg font-medium">{{ capitalizeFirstLetter(plato.name) }} - S/ {{ plato.price }} </span>
                                        <!-- Stock con texto aún más pequeño -->
                                        <div class="text-xs text-gray-500">Stock: {{ plato.quantity }}</div>
                                    </div>
                                    <div class="flex space-x-2">
                                        <Button label="Detalles" icon="pi pi-info-circle" class="p-button-text" @click="verInsumos(plato)" />
                                        <Button
                                            label="Añadir"
                                            icon="pi pi-plus"
                                            class="p-button-success"
                                            :disabled="isPlatoAgregado(plato)"
                                            @click="agregarAlPedido(plato)"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Paginación minimalista y dinámica -->
                        <div class="paginacion mt-4 flex items-center justify-center space-x-6">
                            <!-- Página anterior -->
                            <span v-if="currentPage > 1" class="cursor-pointer text-blue-500" @click="currentPage--">Anterior</span>

                            <!-- Página actual y total -->
                            <span class="text-sm text-gray-600"> Página {{ currentPage }} de {{ totalPages }} </span>

                            <!-- Página siguiente -->
                            <span v-if="currentPage < totalPages" class="cursor-pointer text-blue-500" @click="currentPage++">Siguiente</span>
                        </div>
                    </div>

                    <!-- Columna 2: Pedido y total -->
                    <div class="col-span-1 flex flex-col justify-between pl-4">
                        <h2 class="mb-4 text-lg font-semibold uppercase">
                            <strong>Pedido</strong>
                        </h2>
                        <div class="pedido flex-grow">
                            <!-- Mostrar los platos agregados al pedido -->
                            <div v-for="(plato, index) in order.platos" :key="plato.id" class="mb-2 flex items-center justify-between">
                                <span>{{ capitalizeFirstLetter(plato.name) }} - S/ {{ plato.price }}</span>

                                <!-- Botones para ajustar la cantidad -->
                                <div class="flex items-center space-x-2">
                                    <Button icon="pi pi-minus" class="p-button-text p-button-danger" @click="adjustQuantity(plato, -1)" />
                                    <!-- Mostrar la cantidad actual -->
                                    <span>{{ plato.cantidad }}</span>
                                    <Button icon="pi pi-plus" class="p-button-text p-button-success" @click="adjustQuantity(plato, 1)" />
                                    <span>de {{ plato.quantity }} disponibles</span>
                                </div>

                                <!-- Botón para eliminar el plato -->
                                <Button icon="pi pi-trash" class="p-button-text p-button-danger ml-2" @click="eliminarDelPedido(index)" />
                            </div>
                        </div>

                        <!-- Total y botones al final -->
                        <div class="total mt-4 font-semibold uppercase">
                            <strong>Total: </strong> S/
                            {{ order.platos.reduce((total, plato) => total + parseFloat(plato.price) * plato.cantidad, 0).toFixed(2) }}
                        </div>

                        <!-- Botones debajo del total usando PrimeVue -->
                        <div class="mt-4 flex space-x-4">
                            <Button label="Realizar Pedido" icon="pi pi-check" severity="success" class="w-1/2" @click="realizarPedido" />
                            <Button label="Cancelar Pedido" icon="pi pi-times" severity="danger" class="w-1/2" @click="cancelOrder" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dialog para ver los insumos del plato seleccionado -->
            <Dialog v-model:visible="showInsumosDialog" :header="'Insumos de ' + (selectedPlato?.name || 'Cargando...')" modal width="50%">
                <p class="mb-4 font-semibold">Lista de Insumos:</p>
                <ul class="list-inside list-disc">
                    <li v-for="insumo in selectedPlato?.insumos" :key="insumo.id" class="mb-2">
                        <span class="font-medium">{{ insumo.name }} - </span>
                        <span>{{ insumo.quantityUnitMeasure }} {{ insumo.unitMeasure }}</span>
                    </li>
                </ul>
                <template #footer>
                    <Button label="Cerrar" icon="pi pi-times" severity="secondary" @click="cancelarInsumos" />
                </template>
            </Dialog>
        </div>
    </div>
</template>
