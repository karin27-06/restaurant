<script setup>
import axios from 'axios';
import { debounce } from 'lodash';
import Button from 'primevue/button';
import Column from 'primevue/column';
import DataTable from 'primevue/datatable';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputText from 'primevue/inputtext';
import Select from 'primevue/select';
import { onMounted, ref, watch } from 'vue';

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
import Calendar from 'primevue/calendar';  

const pagination = ref({
    currentPage: 1,
    perPage: 15,
    total: 0,
});
const refreshCount = ref(0); // Variable que se incrementa cuando se agrega un insumo
// Rango de fechas
const dateRange = ref(null); // Variable para el rango de fechas seleccionado

const estadoInputOptions = ref([
    { name: 'TODOS', value: '' },
    { name: 'ACTIVOS', value: 1 },
    { name: 'INACTIVOS', value: 0 },
]);

const isColumnSelected = (fieldName) => {
    return selectedColumns.value.some((col) => col.field === fieldName);
};

const optionalColumns = ref([
    { field: 'tablenum', header: 'Numero' },
    { field: 'capacity', header: 'Capacidad' },
]);

const formatCurrency = (value) => {
    if (value != null) {
        return 'S/. ' + parseFloat(value).toFixed(2);
    }
    return '';
};
// Cargar los datos de los kardex
const loadKardexInputs = async () => {
    loading.value = true;
    try {
        const params = {
            page: pagination.value.currentPage,
            per_page: pagination.value.perPage,
            search: globalFilterValue.value,
            start_date: dateRange.value && dateRange.value[0] ? dateRange.value[0].toISOString().split('T')[0] : null,  // Fecha de inicio
            end_date: dateRange.value && dateRange.value[1] ? dateRange.value[1].toISOString().split('T')[0] : null,  // Fecha de fin
        };

        const response = await axios.get('/insumos/karde', { params });
        inputs.value = response.data.data;
        pagination.value.currentPage = response.data.meta.current_page;
        pagination.value.total = response.data.meta.total;
    } catch (error) {
        console.error('Error al cargar insumos:', error);
    } finally {
        loading.value = false;
    }
};


const props = defineProps({
    refresh: {
        type: Number,
        required: true,
    },
});
// Recarga la tabla cuando `refreshCount` cambia
watch(refreshCount, loadKardexInputs);
watch(() => props.refresh, loadKardexInputs);
watch(
    () => selectedEstadoInput.value,
    () => {
        currentPage.value = 1;
        loadKardexInputs();
    },
);
watch(deleteInputDialog, (val) => {
    console.log('Dialogo eliminar visible:', val);
});

const onPage = (event) => {
    pagination.value.currentPage = event.page + 1;
    pagination.value.perPage = event.rows;
    loadKardexInputs();
};

const onGlobalSearch = debounce(() => {
    pagination.value.currentPage = 1;
    loadKardexInputs();
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
    loadKardexInputs();
}

function handleInputDeleted() {
    loadKardexInputs();
}

onMounted(loadKardexInputs);

// Watcher para el rango de fechas
watch(dateRange, () => {
    // Verificar si ambas fechas están seleccionadas
    if (dateRange.value && dateRange.value[0] && dateRange.value[1]) {
        pagination.value.currentPage = 1;  // Resetear la página a la primera
        loadKardexInputs();  // Realizar la búsqueda automáticamente cuando ambos valores estén seleccionados
    }
});

</script>

<template>
    <DataTable
        ref="dt"
        v-model:selection="selectedInputs"
        :value="inputs"
        dataKey="id"
        :paginator="true"
        :rows="pagination.perPage"
        :totalRecords="pagination.total"
        :loading="loading"
        :lazy="true"
        @page="onPage"
        :rowsPerPageOptions="[15, 20, 25]"
        scrollable
        scrollHeight="574px"
        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
        currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} Kardexes de Insumos"
    >
        <template #header>
            <div class="flex flex-wrap items-center justify-between gap-2">
                <h4 class="m-0">Kardex de Insumos</h4>
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
                        <InputText v-model="globalFilterValue" @input="onGlobalSearch" placeholder="Buscar..." />
                    </IconField>

                    <Button icon="pi pi-refresh" outlined rounded aria-label="Refresh" @click="loadKardexInputs" />
                </div>
            </div>
        </template>

        <Column selectionMode="multiple" style="width: 1rem" :exportable="false"></Column>


<Column field="username" header="Usuario" sortable style="min-width: 7rem;" />
     
              <Column field="movement_type" header="Movimiento" sortable style="min-width: 7rem" />
               <Column field="code" header="Código" sortable style="min-width: 7rem" />
            <Column field="totalPrice" header="Precio Total" sortable style="min-width: 9rem" />
<Column field="quantity" header="Cantidad" sortable style="min-width: 7rem" />
              <Column header="Unidad" sortable style="min-width: 7rem">
            <template #body="{ data }">
                <span>{{ data.quantityUnitMeasure }} {{ data.unitMeasure }}</span>
            </template>
        </Column>
               <Column field="created_at" header="Fecha" sortable style="min-width: 13rem" />
        <Column field="accions" header="Acciones" :exportable="false" style="min-width: 8rem">
            <template #body="{ data }"> <Button icon="pi pi-file-pdf" outlined rounded class="mr-2" @click="generatePDF(data)" /> </template>
        </Column>
    </DataTable>


</template>
