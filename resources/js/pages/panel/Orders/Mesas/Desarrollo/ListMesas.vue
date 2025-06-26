<script setup>
import { router } from '@inertiajs/core';
import axios from 'axios';
import Button from 'primevue/button';
import Column from 'primevue/column';
import DataTable from 'primevue/datatable';
import Dialog from 'primevue/dialog';
import Dropdown from 'primevue/dropdown';
import InputText from 'primevue/inputtext';
import Tag from 'primevue/tag';
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
const showOrderHistorial = ref(false);
const selectedMesa = ref(null);
const platos = ref([]);
const historialPlatos = ref([]);
const historialLoading = ref(false);
const historialPagination = ref({
    page: 1,
    perPage: 7,
    total: 0,
});
const globalFilterValue = ref('');
const platosSeleccionados = ref([]) // aquí se almacenan los platos seleccionados

const order = ref({
    mesaId: null,
    tablenum: null,
    platos: [],
    idOrder: null, // Añadido aquí
});

const selectedPlato = ref(null); // Para almacenar el plato seleccionado

const showReciboToolbar = ref(false);
// Variables de estado para la paginación
const currentPage = ref(1); // Página actual
const perPage = ref(7); // Platos por página
const totalPages = ref(1); // Total de páginas
const loadHistorialPlatos = async () => {
    if (!order.value.idOrder) return;

    historialLoading.value = true;
    try {
        const response = await axios.get(`/order-dishes`, {
            params: {
                idOrder: order.value.idOrder,
                page: historialPagination.value.page,
                per_page: historialPagination.value.perPage,
                search: globalFilterValue.value, 
                state: selectedState.value, 
            },
        });

        historialPlatos.value = response.data.data;
        historialPagination.value.total = response.data.meta.total;
    } catch (error) {
        console.error('Error al cargar historial de platos:', error);
    } finally {
        historialLoading.value = false;
    }
};


// Función para cargar los platos desde la API con búsqueda y paginación
const loadPlatos = async (search = '') => {
    try {
        // Construir la URL con el parámetro de búsqueda y paginación
        const response = await axios.get(`/plato?search=${search}&page=${currentPage.value}&per_page=${perPage.value}`);

        // Filtrar los platos para que solo se muestren aquellos con quantity > 0
        const platosDisponibles = response.data.data.filter(plato => plato.quantity > 0);

        // Asignar solo los platos con cantidad disponible
        platos.value = platosDisponibles;

        // Obtener el total de páginas
        totalPages.value = response.data.meta.last_page; 
    } catch (error) {
        console.error('Error cargando platos:', error);
    }
};

const showOrderFormForMesa = async (mesaId, tablenum) => {
    console.log('Mesa ID:', mesaId);
    console.log('Tablenum:', tablenum);

    selectedMesa.value = mesas.value.find((mesa) => mesa.id === mesaId);
    order.value.mesaId = selectedMesa.value.id;
    order.value.tablenum = tablenum;
    tableNumber.value = tablenum;
    showOrderForm.value = true;
    showOrderHistorial.value = true;
    showReciboToolbar.value = true;

    loadPlatos();

    try {
        // Buscar en la API si hay un pedido activo para esta mesa
        const { data } = await axios.get('/orders');
        const ordenActiva = data.data.find((orden) => orden.idMesa === mesaId && orden.state !== 'finalizado');

        if (ordenActiva) {
            order.value.idOrder = ordenActiva.id;
            await loadHistorialPlatos();
        } else {
            order.value.idOrder = null;
            historialPlatos.value = [];
            historialPagination.value.total = 0;
        }
    } catch (error) {
        console.error('Error obteniendo pedido activo de la mesa:', error);
    }
};

const getSeverity = (state) => {
    switch (state) {
        case 'pendiente':
            return 'warning';
        case 'servido':
            return 'success';
        case 'cancelado':
            return 'danger';
        default:
            return null;
    }
};

const agregarAlPedido = (plato) => {
    // Verifica si el plato ya está en el pedido
    const platoExistente = order.value.platos.find((pedido) => pedido.id === plato.id);

    if (platoExistente) {
        // Si ya existe, solo actualiza la cantidad
        platoExistente.cantidad = Math.min(platoExistente.cantidad + 1, plato.stock);
    } else {
        // Si no existe, agregarlo al pedido con cantidad 1
        order.value.platos.push({
            id: plato.id,
            name: plato.name,
            price: plato.price,
            stock: plato.quantity, // stock máximo
            cantidad: 1, // cantidad inicial en el pedido
        });
    }

    // Imprimir la estructura de los platos en el pedido
    console.log('Platos en el pedido:', order.value.platos);
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

    // Limpiar historial de platos
    historialPlatos.value = [];
    historialPagination.value.total = 0;

    // Ocultar el formulario de pedido y volver a mostrar las mesas
    showOrderForm.value = false;
    showOrderHistorial.value = false;
    showReciboToolbar.value = false;
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
        plato.cantidad = Math.min(Math.max(plato.cantidad + increment, 1), plato.stock);
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
const calcularTotalPedido = () => {
    return order.value.platos.reduce((total, plato) => {
        return total + plato.price * plato.cantidad;
    }, 0);
};

const cancelOrder = () => {
    // Limpiar el pedido (vaciar platos y otros datos)
    order.value.platos = [];
};
// Función para registrar la orden
const realizarPedido = async () => {
    const userId = await fetchUserId();
    try {
        const total = calcularTotalPedido();

        if (order.value.platos.length === 0) {
            toast.add({ severity: 'warn', summary: 'Advertencia', detail: 'Debe seleccionar al menos un plato', life: 3000 });
            return;
        }

        const data = {
            idCustomer: 1,
            idUser: userId,
            idTable: order.value.mesaId,
            totalPrice: total,
            state: 'pendiente',
            platos: order.value.platos.map((plato) => ({
                id: plato.id,
                cantidad: plato.cantidad,
                price: plato.price,
            })),
        };

        const response = await axios.post('/orders', data);

        toast.add({ severity: 'success', summary: 'Éxito', detail: response.data.message, life: 3000 });

        // Guardar el nuevo ID del pedido y recargar historial
        order.value.idOrder = response.data.order.id; // ← backend debe devolver el pedido creado
        order.value.platos = [];
        await loadHistorialPlatos(); // ← recarga historial automáticamente

                await loadPlatos();  // Esto actualiza la lista de platos disponibles (por ejemplo, restando los que se pidieron)

    } catch (error) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'Hubo un problema al registrar la orden', life: 3000 });
        console.error(error);
    }
};

async function fetchUserId() {
    try {
        // Hacemos la solicitud al backend para obtener el user_id
        const { data } = await axios.get('/user-id');

        // Verificamos si la solicitud fue exitosa
        if (data.success) {
            return data.user_id; // Retornamos el user_id
        } else {
            console.error('Error al obtener el user_id');
            return null;
        }
    } catch (e) {
        console.error('Error en la solicitud:', e);
        return null;
    }
}


const fetchOpenOrderIdByMesa = async (mesaId) => {
    try {
        const response = await axios.get('/orders');
        const pedidos = response.data.data || [];

        const pedidoAbierto = pedidos.find((pedido) => pedido.idTable === mesaId && pedido.state !== 'finalizado');

        return pedidoAbierto ? pedidoAbierto.id : null;
    } catch (error) {
        console.error('Error obteniendo el pedido abierto:', error);
        return null;
    }
};

// Agrega la función para el cambio de página
const onHistorialPageChange = (event) => {
    console.log("Cambio de página en el historial:", event); // Imprimir en consola el evento de cambio de página
    historialPagination.value.page = event.page + 1; // Actualiza la página en la paginación
    loadHistorialPlatos(); // Recarga el historial de platos
};

// Asegúrate de que esta función esté definida para el cambio de filas por página
const onRowsPerPageChange = (event) => {
    console.log("Evento de cambio de filas por página recibido", event); // Verifica si el evento se está disparando
    console.log("Filas por página seleccionadas:", event.rows);  // Verifica que se emita el evento correctamente

    historialPagination.value.perPage = event.rows; // Actualiza el número de filas por página
    console.log("Nuevo valor de perPage:", historialPagination.value.perPage);  // Verifica que el valor cambie

    loadHistorialPlatos();  // Recarga los platos
};


// Lógica de búsqueda global
const onGlobalSearch = async () => {
    // Reiniciar la página a 1 cuando se realice una búsqueda
    historialPagination.value.page = 1;

    // Llamar a la API con el filtro de búsqueda global
    await loadHistorialPlatos();
};

const onStateChange = async () => {
    await loadHistorialPlatos();
};

const selectedState = ref(null); // Filtro para el estado
const stateOptions = ref([
    { label: 'Todos los estados', value: null },
    { label: 'Pendiente', value: 'pendiente' },
    { label: 'En Preparación', value: 'en preparación' },
    { label: 'En Entrega', value: 'en entrega' },
    { label: 'Completado', value: 'completado' },
    { label: 'Cancelado', value: 'cancelado' },
]);

const cancelDish = async (dishId) => {
    try {
        const response = await axios.put(`/order-dishes/${dishId}`, { state: 'cancelado' });
        toast.add({ severity: 'success', summary: 'Éxito', detail: 'Platillo cancelado', life: 3000 });

        // Actualiza el historial de platos después de la cancelación
        loadHistorialPlatos();
        loadPlatos();
    } catch (error) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo cancelar el platillo', life: 3000 });
    }
};
const totalHistorialPedido = computed(() => {
    return historialPlatos.value
        .filter((plato) => plato.state !== 'cancelado') // Filtra los platos que no están cancelados
        .reduce((total, plato) => {
            const precio = parseFloat(plato.price) || 0; // Si el precio no es un número válido, se asigna 0
            const cantidad = parseFloat(plato.quantity) || 0; // Si la cantidad no es un número válido, se asigna 0
            return total + precio * cantidad; // Calcula el subtotal
        }, 0) // Inicia la suma desde 0
        .toFixed(2); // Redondea el resultado a dos decimales
});


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
                class="flex items-center justify-center bg-blue-100 rounded-border dark:bg-blue-400/10"
                style="width: 2.5rem; height: 2.5rem"
                @click="showOrderFormForMesa(mesa.id, mesa.tablenum)"
            >
                <!-- Condición para mostrar icono según el estado de la mesa -->
                <i
                    v-if="mesa.order_status === 'disponible'"
                    class="pi pi-plus !text-xl text-blue-500"
                    style="cursor: pointer !important;"
                ></i>
                <i
                    v-else
                    class="pi pi-clock !text-xl text-yellow-500"
                    style="cursor: not-allowed !important;"
                ></i>
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
                                    <span>de {{ plato.stock }} disponibles</span>
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
                            <Button label="Vaciar" icon="pi pi-times" severity="danger" class="w-1/2" @click="cancelOrder" />
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="showOrderHistorial" class="card">
<DataTable
  v-if="order.idOrder"
  :value="historialPlatos"
  :loading="historialLoading"
  :lazy="true"
  :paginator="true"
  :rows="historialPagination.perPage"
  :first="(historialPagination.page - 1) * historialPagination.perPage"
  :totalRecords="historialPagination.total"
  dataKey="id"
  scrollable
  scrollHeight="400px"
  class="mt-6"
  @page="onHistorialPageChange"
  

>


        <template #header>
            <div class="flex flex-wrap items-center justify-between gap-2">
                <h4 class="m-0">HISTORIAL DE LA ORDEN</h4>
                <div class="flex flex-wrap gap-2">
                   <!-- Dropdown for Floor -->
              
                    <IconField>
                        <InputText v-model="globalFilterValue" @input="onGlobalSearch" placeholder="Buscar..." />
                    </IconField>
                <Dropdown
                    v-model="selectedState"
                    :options="stateOptions"
                    option-label="label"
                    option-value="value"
                    placeholder="Selecciona un Estado"
                    class="w-1/1"
                    @change="onStateChange"
                />
                    <Button icon="pi pi-refresh" outlined rounded aria-label="Refresh" @click="loadHistorialPlatos" />
                </div>
            </div>
       
        </template>

                    <Column field="name" header="Nombre" />
                    <Column field="quantity" header="Cantidad" />
                    <Column field="price" header="Precio Unit.">
                        <template #body="{ data }"> S/ {{ parseFloat(data.price).toFixed(2) }} </template>
                    </Column>
                            <!-- Nueva columna "Subtotal" -->
        <Column header="Subtotal" style="min-width: 8rem">
            <template #body="{ data }">
                S/ {{ (parseFloat(data.quantity) * parseFloat(data.price)).toFixed(2) }}
            </template>
        </Column>

                    <Column field="state" header="Estado">
                    <template #body="{ data }">
                        <Tag :value="data.state" :severity="getSeverity(data.state)" />
                        <Button v-if="data.state === 'pendiente'" label="Cancelar" icon="pi pi-times" severity="danger" class="p-button-text" @click="cancelDish(data.id)" />
                    </template>
                </Column>
                    <Column field="creacion" header="Fecha de creación" />
                </DataTable>
                  <!-- Mostrar total del historial de la orden, excluyendo los cancelados -->
            <div class="p-4 text-right font-semibold">
                <span>Total: </span>
                <span>S/ {{ totalHistorialPedido }}</span>
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
