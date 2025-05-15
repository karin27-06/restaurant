<script setup>
import { ref, watch, onMounted } from 'vue';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import Checkbox from 'primevue/checkbox';
import Tag from 'primevue/tag';
import axios from 'axios';
import { useToast } from 'primevue/usetoast';
import Select from 'primevue/select';

const props = defineProps({
    visible: Boolean,
    clienteId: Number
});
const emit = defineEmits(['update:visible', 'updated']);

const toast = useToast();
const dialogVisible = ref(props.visible);
const loading = ref(false);
const submitted = ref(false);
const serverErrors = ref({});

const cliente = ref({
    name: '',
    codigo: '',
    client_type_id: null,
    state: false,
});

const tiposCliente = ref([]);

watch(() => props.visible, (val) => {
    dialogVisible.value = val;
    if (val && props.clienteId) {
        fetchCliente();
        fetchTiposCliente();
    }
});
watch(dialogVisible, (val) => emit('update:visible', val));

const fetchCliente = async () => {
    try {
        loading.value = true;
        const res = await axios.get(`/cliente/${props.clienteId}`);
        const data = res.data.customer;
        cliente.value = {
            name: data.name,
            codigo: data.codigo,
            client_type_id: data.client_type_id,
            state: data.state
        };
    } catch (error) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo cargar el cliente', life: 3000 });
        console.error(error);
    } finally {
        loading.value = false;
    }
};

const fetchTiposCliente = async () => {
    try {
        const res = await axios.get('/tipo_cliente');
        tiposCliente.value = res.data.data;
    } catch (error) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo cargar los tipos de cliente', life: 3000 });
        console.error(error);
    }
};

const updateCliente = async () => {
    submitted.value = true;
    serverErrors.value = {};

    try {
        const clienteData = {
            name: cliente.value.name,
            codigo: cliente.value.codigo,
            client_type_id: cliente.value.client_type_id,
            state: cliente.value.state
        };

        await axios.put(`/cliente/${props.clienteId}`, clienteData);

        toast.add({
            severity: 'success',
            summary: 'Actualizado',
            detail: 'Cliente actualizado correctamente',
            life: 3000
        });

        dialogVisible.value = false;
        emit('updated');
    } catch (error) {
        if (error.response && error.response.data?.errors) {
            serverErrors.value = error.response.data.errors;
            toast.add({
                severity: 'error',
                summary: 'Error de validación',
                detail: 'Revisa los campos e intenta nuevamente.',
                life: 5000
            });
        } else {
            toast.add({
                severity: 'error',
                summary: 'Error',
                detail: 'No se pudo actualizar el cliente',
                life: 3000
            });
        }
        console.error(error);
    }
};
</script>

<template>
    <Dialog v-model:visible="dialogVisible" header="Editar Cliente" modal :closable="true" :style="{ width: '600px' }">
        <div class="flex flex-col gap-6">
            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-10">
                    <label class="block font-bold mb-2">Código <span class="text-red-500">*</span></label>
                    <InputText
                        v-model="cliente.codigo"
                        required
                        fluid
                        maxlength="50"
                        :class="{ 'p-invalid': serverErrors.codigo }"
                    />
                    <small v-if="serverErrors.codigo" class="p-error">{{ serverErrors.codigo[0] }}</small>
                </div>
                <div class="col-span-2">
                    <label class="block font-bold mb-2">Estado</label>
                    <div class="flex items-center gap-3">
                        <Checkbox v-model="cliente.state" :binary="true" />
                        <Tag :value="cliente.state ? 'Activo' : 'Inactivo'" :severity="cliente.state ? 'success' : 'danger'" />
                    </div>
                </div>
                <div class="col-span-12">
                    <label class="block font-bold mb-2">Nombre <span class="text-red-500">*</span></label>
                    <InputText
                        v-model="cliente.name"
                        required
                        maxlength="100"
                        fluid
                        :class="{ 'p-invalid': serverErrors.name }"
                    />
                    <small v-if="serverErrors.name" class="p-error">{{ serverErrors.name[0] }}</small>
                </div>
                <div class="col-span-12">
                    <label class="block font-bold mb-2">Tipo de Cliente <span class="text-red-500">*</span></label>
                    <Select
                        v-model="cliente.client_type_id"
                        :options="tiposCliente"
                        optionLabel="name"
                        optionValue="id"
                        placeholder="Seleccione"
                        class="w-full"
                        fluid
                        :class="{ 'p-invalid': serverErrors.client_type_id }"
                    />
                    <small v-if="serverErrors.client_type_id" class="p-error">{{ serverErrors.client_type_id[0] }}</small>
                </div>
            </div>
        </div>
        <template #footer>
            <Button label="Cancelar" icon="pi pi-times" text @click="dialogVisible = false" />
            <Button label="Guardar" icon="pi pi-check" @click="updateCliente" :loading="loading" />
        </template>
    </Dialog>
</template>
