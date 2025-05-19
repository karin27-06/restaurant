<script setup>
import { ref, watch } from 'vue';
import Dialog from 'primevue/dialog';
import Button from 'primevue/button';
import axios from 'axios';
import { useToast } from 'primevue/usetoast';

const props = defineProps({
    visible: Boolean,
    plato: {
        type: Object,
        default: () => ({})
    }
});

const emit = defineEmits(['update:visible', 'deleted']);

const dialogVisible = ref(props.visible);
const loading = ref(false);
const toast = useToast();

// Sync dialog visibility with prop
watch(() => props.visible, (val) => dialogVisible.value = val);
watch(dialogVisible, (val) => emit('update:visible', val));

const deletePlato = async () => {
    loading.value = true;
    
    try {
        await axios.delete(`/plato/${props.plato.id}`);
        
        toast.add({
            severity: 'success',
            summary: 'Eliminado',
            detail: 'Plato eliminado correctamente',
            life: 3000
        });
        
        dialogVisible.value = false;
        emit('deleted');
    } catch (error) {
        console.error('Error al eliminar plato:', error);
        
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: 'No se pudo eliminar el plato',
            life: 3000
        });
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <Dialog v-model:visible="dialogVisible" header="Confirmar eliminación" modal :style="{ width: '450px' }">
        <div class="flex flex-col align-items-center p-5">
            <h3>¿Estás seguro de eliminar este plato?</h3>
            <p class="text-center my-3">
                <strong>Nombre:</strong> {{ plato.name }}
            </p>
            <p class="text-center my-1">
                Esta acción no se puede deshacer.
            </p>
        </div>
        <template #footer>
            <Button label="No" icon="pi pi-times" outlined @click="dialogVisible = false" />
            <Button label="Sí, eliminar" icon="pi pi-check" severity="danger" @click="deletePlato" :loading="loading" />
        </template>
    </Dialog>
</template>
