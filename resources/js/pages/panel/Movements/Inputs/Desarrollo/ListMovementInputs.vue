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
import { debounce } from 'lodash';
import DeleteMovementInput from './DeleteMovementInput.vue';
import UpdateMovementInput from './UpdateMovementInput.vue';

const movementInputs = ref([]);
const loading = ref(false);
const globalFilterValue = ref('');
const deleteMovementInputDialog = ref(false);
const updateMovementInputDialog = ref(false);
const selectedMovementInputId = ref(null);
const movementInput = ref({});
const currentPage = ref(1);
const selectedColumns = ref([]);
const selectedSupplier = ref(null);
const selectedEstadoMovementInput = ref(null);
const pagination = ref({
    currentPage: 1,
    perPage: 15,
    total: 0
});

const refreshCount = ref(0);  // Variable que se incrementa cuando se agrega un movimiento



const isColumnSelected = (fieldName) => {
    return selectedColumns.value.some(col => col.field === fieldName);
};

const formatCurrency = (value) => {
    if (value != null) {
        return 'S/. ' + parseFloat(value).toFixed(2);
    }
    return '';
};

const loadMovementInputs = async () => {
    loading.value = true;
    try {
        const params = {
            page: pagination.value.currentPage,
            per_page: pagination.value.perPage,
            search: globalFilterValue.value,
            supplier: selectedSupplier?.value,
            state: selectedEstadoMovementInput.value?.value ?? '',
        };
        const response = await axios.get('/insumos/movimiento', { params });
        movementInputs.value = response.data.data;
        pagination.value.currentPage = response.data.meta.current_page;
        pagination.value.total = response.data.meta.total;
    } catch (error) {
        console.error('Error al cargar los movimientos de entrada:', error);
    } finally {
        loading.value = false;
    }
};

const props = defineProps({
    refresh: {
        type: Number,
        required: true
    }
});
// Recarga la tabla cuando `refreshCount` cambia
watch(refreshCount, loadMovementInputs);
watch(() => props.refresh, loadMovementInputs);
watch(() => selectedEstadoMovementInput.value, () => {
    currentPage.value = 1;
    loadMovementInputs();
});

const onPage = (event) => {
    pagination.value.currentPage = event.page + 1;
    pagination.value.perPage = event.rows;
    loadMovementInputs();
};

const onGlobalSearch = debounce(() => {
    pagination.value.currentPage = 1;
    loadMovementInputs();
}, 500);

const getSeverity = (value) => {
    return value ? 'success' : 'danger';
};

const editarMovementInput = (movement) => {
    selectedMovementInputId.value = movement.id;
    updateMovementInputDialog.value = true;
};

const confirmarDeleteMovementInput = (movement) => {
    movementInput.value = movement;
    console.log(movementInput.value); // Verifica si los datos están correctos
    deleteMovementInputDialog.value = true;
};

function handleMovementInputUpdated() {
    loadMovementInputs();
}

function handleMovementInputDeleted() {
    loadMovementInputs();
}

onMounted(loadMovementInputs);
const getMovementTypeLabel = (value) => {
  const movementTypes = {
    1: 'Factura',
    2: 'Guía',
    3: 'Boleta',
  };
  return movementTypes[value] || 'Desconocido'; // Valor por defecto si no encuentra el tipo
};



</script>

<template>
    <DataTable ref="dt" v-model:selection="selectedMovementInputs" :value="movementInputs" dataKey="id" :paginator="true"
        :rows="pagination.perPage" :totalRecords="pagination.total" :loading="loading" :lazy="true" @page="onPage"
        :rowsPerPageOptions="[15, 20, 25]" scrollable scrollHeight="574px"
        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
        currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} Movimientos de Insumo">
        <template #header>
            <div class="flex flex-wrap gap-2 items-center justify-between">
                <h4 class="m-0">Movimientos de Insumos</h4>
                <div class="flex flex-wrap gap-2">
                    <IconField>
                        <InputIcon>
                            <i class="pi pi-search" />
                        </InputIcon>
                        <InputText v-model="globalFilterValue" @input="onGlobalSearch" placeholder="Buscar..." />
                    </IconField>
                    
                    <Button icon="pi pi-refresh" outlined rounded aria-label="Refresh" @click="loadMovementInputs" />
                </div>
            </div>
        </template>
        <Column selectionMode="multiple" style="width: 1rem" :exportable="false"></Column>

        <Column field="code" header="Código" sortable style="min-width: 7rem" />
        <Column field="movement_type" header="Tipo" sortable style="min-width: 5rem">
  <template #body="{ data }">
    <span>
      {{ getMovementTypeLabel(data.movement_type) }}
    </span>
  </template>
</Column>
        <Column field="supplier_name" header="Proveedor" sortable style="min-width: 14rem" />
        <Column field="payment_type" header="Pago" sortable style="min-width: 6rem" />
        <Column field="issue_date" header="Emisión" sortable style="min-width: 8rem" />
        <Column field="execution_date" header="Ejecución" sortable style="min-width: 7rem" />
        <Column field="sub" header="Sub" sortable style="min-width: 5rem" />
        <Column field="igv" header="IGV" sortable style="min-width: 5rem" />
        <Column field="total" header="Total" sortable style="min-width: 5rem" />

       
        <Column field="created_at" header="Creación" sortable style="min-width: 13rem" />
        <Column field="updated_at" header="Actualización" sortable style="min-width: 13rem"/>
        <Column field="accions" header="Acciones" :exportable="false" style="min-width: 10rem">
            <template #body="{ data }">
                <Button icon="pi pi-pencil" outlined rounded class="mr-2" @click="editarMovementInput(data)" />
                <Button icon="pi pi-trash" outlined rounded severity="danger" @click="confirmarDeleteMovementInput(data)" />
            </template>
        </Column>
    </DataTable>

    <DeleteMovementInput
        v-model:visible="deleteMovementInputDialog"
        :movementInput="movementInput"
        @deleted="handleMovementInputDeleted"
    />
    <UpdateMovementInput
        v-model:visible="updateMovementInputDialog"
        :movementInputId="selectedMovementInputId"
        @updated="handleMovementInputUpdated"
    />

    
</template>
