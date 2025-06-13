<script setup>
import { ref, watch } from 'vue';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import Checkbox from 'primevue/checkbox';
import Select from 'primevue/select';
import Tag from 'primevue/tag';
import { useToast } from 'primevue/usetoast';
import axios from 'axios';

const props = defineProps({
    visible: Boolean,
    cajaId: Number
});
const emit = defineEmits(['update:visible', 'updated']);

const toast = useToast();
const serverErrors = ref({});
const submitted = ref(false);
const loading = ref(false);

const dialogVisible = ref(props.visible); // Mantener la visibilidad del modal controlada por el padre

watch(() => props.visible, (val) => dialogVisible.value = val); // Actualizar cuando la propiedad 'visible' cambie desde el componente padre

watch(dialogVisible, (val) => emit('update:visible', val)); // Enviar cambios de visibilidad al componente padre

const caja = ref({
    state: false,
    idVendedor: null
});

const vendedores = ref([]);

watch(() => props.visible, async (val) => {
    if (val && props.cajaId) {
        await fetchCaja();
        await fetchVendedores();
    }
});

const fetchCaja = async () => {
    loading.value = true;
    try {
        const { data } = await axios.get(`/caja/${props.cajaId}`);
        const c = data.caja;
        caja.value = {
            state: c.state,
            numero_cajas: c.numero_cajas,  // Asignamos el número de caja
            idVendedor: c.idVendedor,
        };
    } catch (error) {
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: 'No se pudo cargar la caja',
            life: 3000
        });
    } finally {
        loading.value = false;
    }
};

const fetchVendedores = async () => {
    try {
        const { data } = await axios.get('/usuarios', { params: { state: 1 } });
        // Modificando los datos para que el label sea el nombre completo del vendedor
        vendedores.value = data.map(v => ({ label: v.name1, value: v.id }));
    } catch (e) {
        toast.add({ severity: 'warn', summary: 'Advertencia', detail: 'No se pudieron cargar los vendedores' });
    }
};


const updateCaja = async () => {
    submitted.value = true;
    serverErrors.value = {};

    try {
        const dataToSend = {
            state: caja.value.state === true,  // Mantén el estado correcto
            idVendedor: caja.value.idVendedor
        };

        await axios.put(`/caja/${props.cajaId}`, dataToSend);

        toast.add({
            severity: 'success',
            summary: 'Actualizado',
            detail: 'Caja actualizada correctamente',
            life: 3000
        });

        // Aquí actualizas el estado de la lista de cajas en el frontend
        dialogVisible.value = false;
        emit('updated');
    } catch (error) {
        if (error.response?.data?.errors) {
            serverErrors.value = error.response.data.errors;
            toast.add({
                severity: 'error',
                summary: 'Error de validación',
                detail: error.response.data.message || 'Revisa los campos.',
                life: 5000
            });
        } else {
            toast.add({
                severity: 'error',
                summary: 'Error',
                detail: 'No se pudo actualizar la caja',
                life: 3000
            });
        }
    }
};
</script>

<template>
    <Dialog v-model:visible="dialogVisible" header="Editar Caja" modal :closable="true" :closeOnEscape="true" :style="{ width: '500px' }">
        <div class="flex flex-col gap-6">
            <div class="grid grid-cols-12 gap-4">
                <!-- Número de caja -->
                <div class="col-span-9">
                    <label class="block font-bold mb-2">Número de caja</label>
                    <InputText v-model="caja.numero_cajas" readonly fluid />
                </div>

                <!-- Estado -->
                <div class="col-span-2">
                    <label class="block font-bold mb-2">Estado <span class="text-red-500">*</span></label>
                    <div class="flex items-center gap-3">
                        <Checkbox v-model="caja.state" :binary="true" fluid />
                        <Tag :value="caja.state ? 'Sin ocupar' : 'Ocupada'"
                            :severity="caja.state ? 'success' : 'danger'" />
                    </div>
                </div>

                <!-- Vendedor-->
                <div class="col-span-12">
                    <label class="block font-bold mb-2">Vendedor <span class="text-red-500">*</span></label>
                    <Select v-model="caja.idVendedor" :options="vendedores" optionLabel="label"
                        optionValue="value" placeholder="Seleccione vendedor" fluid
                        :class="{ 'p-invalid': serverErrors.idVendedor }" />
                    <small v-if="serverErrors.idVendedor" class="p-error">{{ serverErrors.idVendedor[0] }}</small>
                </div>

            </div>
        </div>

        <template #footer>
            <Button label="Cancelar" icon="pi pi-times" text @click="dialogVisible = false" />
            <Button label="Guardar" icon="pi pi-check" @click="updateCaja" :loading="loading" />
        </template>
    </Dialog>
</template>
