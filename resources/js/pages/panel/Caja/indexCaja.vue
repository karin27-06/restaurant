<template>
    <Head title="Cajas" />
    <AppLayout>
        <div>
            <template v-if="isLoading">
                <Espera/>
            </template>

            <template v-else>
                <div class="card">
                    <div class="flex justify-between items-center mb-4">
                        <AddCajas @caja-agregada="refrescarListado"/>
                        <Button 
                            label="Aperturar Caja" 
                            icon="pi pi-lock-open" 
                            severity="success"
                            @click="goToApertura"
                        />
                    </div>
                    <ListCajas :refresh="refreshKey"/>
                </div>
            </template>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
//import { router } from '@inertiajs/vue3';
import AppLayout from '@/layout/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import Espera from '@/components/Espera.vue';
import ListCajas from './Desarrollo/ListCajas.vue';
import AddCajas from './Desarrollo/AddCajas.vue';
import Button from 'primevue/button';

const isLoading = ref(true);
const refreshKey = ref(0);

function refrescarListado() {
    refreshKey.value++;
}
function goToApertura() {
    window.location.href = '/caja/aperturar';
}
onMounted(() => {
    setTimeout(() => {
        isLoading.value = false;
    }, 1000);
});
</script>
