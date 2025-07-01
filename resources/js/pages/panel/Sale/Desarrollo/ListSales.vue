<script setup>
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';
import Button from 'primevue/button';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputText from 'primevue/inputtext';
import Tag from 'primevue/tag';
import Select from 'primevue/select';
import { debounce } from 'lodash';
import DeleteInput from './DeleteInputs.vue';
import UpdateInput from './UpdateInputs.vue';
import MultiSelect from 'primevue/multiselect';
import Calendar from 'primevue/calendar';  

const inputs = ref([]);
const loading = ref(false);
const globalFilterValue = ref('');
const deleteInputDialog = ref(false);
const updateInputDialog = ref(false);
const selectedInputId = ref(null);
const input = ref({});
const currentPage = ref(1);
const selectedColumns = ref([]);
const selectedAlmacen = ref(null);
const selectedEstadoInput = ref(null);
import Dialog from 'primevue/dialog';  // Importar Dialog para el modal
const selectedSale = ref(null);  // Variable para almacenar la venta seleccionada
const detailsDialog = ref(false);  // Controlar la visibilidad del modal
const pagination = ref({
    currentPage: 1,
    perPage: 15,
    total: 0
});
const refreshCount = ref(0);  // Variable que se incrementa cuando se agrega un insumo

// Método para ver los detalles de una venta
const verDetalle = async (data) => {
    try {
        // Solicitar los detalles de la venta usando idSale
        const response = await axios.get(`/venta?idSale=${data.idSale}`);
        selectedSale.value = response.data.data[0];  // Almacenar los detalles de la venta
        detailsDialog.value = true;  // Abrir el modal
    } catch (error) {
        console.error('Error al obtener detalles de la venta:', error);
    }
};
const estadoInputOptions = ref([
    { name: 'TODOS', value: '' },
    { name: 'ACTIVOS', value: 1 },
    { name: 'INACTIVOS', value: 0 },
]);

const isColumnSelected = (fieldName) => {
    return selectedColumns.value.some(col => col.field === fieldName);
};

const optionalColumns = ref([
    { field: 'tablenum', header: 'Numero' },
    { field: 'capacity', header: 'Capacidad' }

]);




const dateRange = ref(null); // Variable para almacenar el rango de fechas

const loadInputs = async () => {
    loading.value = true;
    try {
        const params = {
            page: pagination.value.currentPage,
            per_page: pagination.value.perPage,
            start_date: dateRange.value ? dateRange.value[0].toISOString().split('T')[0] : null,
            end_date: dateRange.value ? dateRange.value[1].toISOString().split('T')[0] : null,
            search: globalFilterValue.value,
        };
        const response = await axios.get('/venta', { params });
        inputs.value = response.data.data;
        pagination.value.currentPage = response.data.meta.current_page;
        pagination.value.total = response.data.meta.total;
    } catch (error) {
        console.error('Error al cargar insumos:', error);
    } finally {
        loading.value = false;
    }
};

const verRecibo = async () => {
    if (selectedSale.value && selectedSale.value.order) {
        const idOrder = selectedSale.value.order.id; // Usar selectedSale para obtener el idOrder
        window.location.href = `/venta/pdf/${idOrder}`; // Redirigir al PDF
    } else {
        console.error("No se ha seleccionado ninguna venta.");
    }
};

// Método que se ejecuta cuando se selecciona un rango de fechas
const onDateRangeChange = () => {
    pagination.value.currentPage = 1; // Reiniciar la página al primer conjunto de resultados
    loadInputs();
};

// Watcher para que la tabla se actualice cada vez que el rango de fechas cambie
watch(dateRange, loadInputs, { immediate: true });

const props = defineProps({
    refresh: {
        type: Number,
        required: true
    }
});
// Recarga la tabla cuando `refreshCount` cambia
watch(refreshCount, loadInputs);
watch(() => props.refresh, loadInputs);
watch(() => selectedEstadoInput.value, () => {
    currentPage.value = 1;
    loadInputs();
});
watch(deleteInputDialog, (val) => {
    console.log('Dialogo eliminar visible:', val);
});

const onPage = (event) => {
    pagination.value.currentPage = event.page + 1;
    pagination.value.perPage = event.rows;
    loadInputs();
};

const onGlobalSearch = debounce(() => {
    pagination.value.currentPage = 1;
    loadInputs();
}, 500);

const getSeverity = (value) => {
    return value ? 'success' : 'danger';
};

const editarInput = (prod) => {
    selectedInputId.value = prod.id;
    updateInputDialog.value = true;
};

const confirmarDeleteInput = (prod) => {
    input.value = prod;
    deleteInputDialog.value = true;
};



function handleInputUpdated() {
    loadInputs();
}

function handleInputDeleted() {
    loadInputs();
}

onMounted(loadInputs);


</script>

<template>
    <DataTable ref="dt" v-model:selection="selectedInputs" :value="inputs" dataKey="id" :paginator="true"
        :rows="pagination.perPage" :totalRecords="pagination.total" :loading="loading" :lazy="true" @page="onPage"
        :rowsPerPageOptions="[15, 20, 25]" scrollable scrollHeight="574px"
        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
        currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} Ventas">
        <template #header>
            <div class="flex flex-wrap gap-2 items-center justify-between">
                <h4 class="m-0">Ventas</h4>
                <div class="flex flex-wrap gap-2">
                    <div class="flex gap-2">
                        <Calendar 
                            v-model="dateRange" 
                            selectionMode="range" 
                            placeholder="Rango de fechas" 
                            class="w-full"
                            dateFormat="yy-mm-dd"
                            @change="onDateRangeChange"  />
                    </div>
                    <IconField>
                        <InputIcon>
                            <i class="pi pi-search" />
                        </InputIcon>
                        <InputText v-model="globalFilterValue" @input="onGlobalSearch" placeholder="Buscar por DNI o RUC" />
                    </IconField>

                    <Button icon="pi pi-refresh" outlined rounded aria-label="Refresh" @click="loadInputs" />
                </div>
            </div>
        </template>

                <Column selectionMode="multiple" style="width: 1rem" :exportable="false"></Column>

        <Column field="salesInvoice.serie" header="Nº" sortable style="min-width: 7rem" />
        <Column field="sale.customer.name" header="Cliente" sortable style="min-width: 10rem" />
        <Column field="order.numeroMesa" header="Nº Mesa" sortable style="min-width: 10rem" />
        <Column field="sale.documentType" header="Tipo" sortable style="min-width: 7rem" />
        <Column field="sale.paymentType" header="Metodo" sortable style="min-width: 7rem" />
       <Column field="subtotal" header="Total" sortable style="min-width: 10rem" />
       <Column field="sale.stateSunat" header="Sunat" sortable style="min-width: 10rem" />
        <Column field="sale.created_at" header="Creación" sortable style="min-width: 13rem" />
        <Column field="sale.updated_at" header="Actualización" sortable style="min-width: 13rem"/>
        <Column field="accions" header="Acciones" :exportable="false" style="min-width: 8rem">
            <template #body="{ data }">
                <Button icon="pi pi-eye" outlined rounded @click="verDetalle(data)" />
            </template>
        </Column>
    </DataTable>

<Dialog v-model:visible="detailsDialog" header="Detalles de Venta" :closable="false">
    <div v-if="selectedSale">
        <!-- Contenedor para los datos del cliente en fila horizontal -->
        <div class="flex flex-wrap gap-4">
            <p><strong>Cliente:</strong> {{ selectedSale.sale.customer.name }}</p>
            <p><strong>Código:</strong> {{ selectedSale.sale.customer.codigo }}</p>
            <p><strong>Comprobante:</strong> {{ selectedSale.salesInvoice.serie }}</p>
            <p><strong>Documento:</strong> {{ selectedSale.sale.documentType }}</p>
            <p><strong>Nº Order:</strong> {{ selectedSale.order.id }}</p>
            <p><strong>Mesa:</strong> {{ selectedSale.order.numeroMesa }}</p>
            <p><strong>Estado:</strong> {{ selectedSale.order.state }}</p>
        </div>

        <!-- DataTable para mostrar los platos -->
        <DataTable :value="selectedSale.order.orderDishes" :responsive="true">
            <Column field="name" header="Plato" />
            <Column field="quantity" header="Cantidad" />
            <Column field="price" header="Precio" />
            <Column field="subtotal" header="Subtotal" />
            <Column field="state" header="Estado" />
            <Column field="creacion" header="Fecha" />
        </DataTable>

        <!-- Total alineado a la derecha -->
        <br>
        <p style="text-align: right; font-weight: bold;">
            <strong>Total:</strong> {{ selectedSale.subtotal }}
        </p>

        <!-- Botones para Ver Recibo o Cerrar -->
        <div class="flex justify-end gap-3 mt-4">
            <Button label="Ver Recibo" icon="pi pi-file-pdf" @click="verRecibo" />
            <Button label="Cerrar" icon="pi pi-times" @click="detailsDialog = false" class="p-button-text" />
        </div>
    </div>
</Dialog>


</template>
