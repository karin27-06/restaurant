<template>
    <Toolbar class="mb-6">
        <template #start>
            <Button label="Nuevo" icon="pi pi-plus" severity="secondary" class="mr-2" @click="openNew" />
        </template>
    </Toolbar>

    <Dialog v-model:visible="clienteDialog" :style="{ width: '600px' }" header="Registro de cliente" :modal="true">
        <div class="flex flex-col gap-6">
            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-10">
                    <label class="block font-bold mb-2">Nombre <span class="text-red-500">*</span></label>
                    <InputText
                        v-model.trim="cliente.name"
                        required
                        maxlength="100"
                        fluid
                    />
                    <small v-if="submitted && !cliente.name" class="text-red-500">El nombre es obligatorio.</small>
                    <small v-if="serverErrors.name" class="text-red-500">{{ serverErrors.name[0] }}</small>
                </div>

                <div class="col-span-2">
                    <label class="block font-bold mb-2">Estado <span class="text-red-500">*</span></label>
                    <div class="flex items-center gap-3">
                        <Checkbox v-model="cliente.state" :binary="true" />
                        <Tag :value="cliente.state ? 'Activo' : 'Inactivo'" :severity="cliente.state ? 'success' : 'danger'" />
                    </div>
                    <small v-if="serverErrors.state" class="text-red-500">{{ serverErrors.state[0] }}</small>
                </div>

                <div class="col-span-12">
                    <label class="block font-bold mb-2">Código <span class="text-red-500">*</span></label>
                    <InputText
                        v-model.trim="cliente.codigo"
                        required
                        fluid
                        maxlength="50"
                    />
                    <small v-if="submitted && !cliente.codigo" class="text-red-500">El código es obligatorio.</small>
                    <small v-if="serverErrors.codigo" class="text-red-500">{{ serverErrors.codigo[0] }}</small>
                </div>

                <div class="col-span-12">
                    <label class="block font-bold mb-2">Tipo de Cliente <span class="text-red-500">*</span></label>
                    <Select
                        v-model="cliente.client_type_id"
                        :options="tiposCliente"
                        optionLabel="name"
                        optionValue="id"
                        fluid
                        placeholder="Seleccione"
                        class="w-full"
                    />
                    <small v-if="submitted && !cliente.client_type_id" class="text-red-500">Debe seleccionar un tipo.</small>
                    <small v-if="serverErrors.client_type_id" class="text-red-500">{{ serverErrors.client_type_id[0] }}</small>
                </div>
            </div>
        </div>

        <template #footer>
            <Button label="Cancelar" icon="pi pi-times" text @click="hideDialog" />
            <Button label="Guardar" icon="pi pi-check" @click="guardarCliente" />
        </template>
    </Dialog>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import Dialog from 'primevue/dialog';
import Toolbar from 'primevue/toolbar';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Checkbox from 'primevue/checkbox';
import Tag from 'primevue/tag';
import { useToast } from 'primevue/usetoast';
import Select from 'primevue/select';

const toast = useToast();
const submitted = ref(false);
const clienteDialog = ref(false);
const serverErrors = ref({});
const tiposCliente = ref([]);

const emit = defineEmits(['cliente-agregado']);

const cliente = ref({
    name: '',
    codigo: '',
    client_type_id: null,
    state: true
});

function resetCliente() {
    cliente.value = {
        name: '',
        codigo: '',
        client_type_id: null,
        state: true
    };
    serverErrors.value = {};
    submitted.value = false;
}

function openNew() {
    resetCliente();
    clienteDialog.value = true;
    fetchTiposCliente();
}

function hideDialog() {
    clienteDialog.value = false;
    resetCliente();
}

function fetchTiposCliente() {
    axios.get('/tipo_cliente')
        .then(res => {
            tiposCliente.value = res.data.data;
        })
        .catch(err => {
            toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo cargar los tipos de cliente', life: 3000 });
        });
}

function guardarCliente() {
    submitted.value = true;
    serverErrors.value = {};

    if (!cliente.value.name || !cliente.value.codigo || !cliente.value.client_type_id) return;

    axios.post('/cliente', cliente.value)
        .then(response => {
            toast.add({ severity: 'success', summary: 'Éxito', detail: 'Cliente registrado', life: 3000 });
            hideDialog();
            emit('cliente-agregado');
        })
        .catch(error => {
            if (error.response && error.response.status === 422) {
                serverErrors.value = error.response.data.errors || {};
            } else {
                toast.add({
                    severity: 'error',
                    summary: 'Error',
                    detail: 'No se pudo registrar el cliente',
                    life: 3000
                });
            }
        });
}
</script>
