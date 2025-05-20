<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import AppMenuItem from './AppMenuItem.vue';

const page = usePage();
const permissions = computed(() => page.props.auth.user?.permissions ?? []);
const hasPermission = (perm) => permissions.value.includes(perm);

const model = computed(() => [
    {
        label: 'Home',
        items: [
            { label: 'Dashboard', icon: 'pi pi-fw pi-home', to: '/dashboard' }
        ]
    },
    {
        label: 'Gestión de Productos',
        items: [
            hasPermission('ver productos') && { label: 'Productos', icon: 'pi pi-fw pi-box', to: '/productos' },
            hasPermission('ver categorias') && { label: 'Categorías', icon: 'pi pi-fw pi-tags', to: '/categorias' },
            hasPermission('ver almacenes') && { label: 'Almacenes', icon: 'pi pi-fw pi-building', to: '/almacenes' },
            hasPermission('ver proveedores') && { label: 'Proveedores', icon: 'pi pi-fw pi-truck', to: '/proveedores' },
        ].filter(Boolean),
    },
    {
        label: 'Gestión de Cocina',
        items: [
            hasPermission('ver dishes') && { label: 'Platos', icon: 'pi pi-fw pi-apple', to: '/platos' },
        ].filter(Boolean),
    },
    {
        label: 'Gestión de Clientes',
        items: [
            hasPermission('ver clientes') && { label: 'Clientes', icon: 'pi pi-fw pi-users', to: '/clientes' },
            hasPermission('ver tipos_clientes') && { label: 'Tipo de Clientes', icon: 'pi pi-fw pi-id-card', to: '/tipo_clientes' },
        ].filter(Boolean),
    },
    {
        label: 'Gestión de Infraestructura',
        items: [
            hasPermission('ver pisos') && { label: 'Pisos', icon: 'pi pi-fw pi-building', to: '/pisos' },
            hasPermission('ver areas') && { label: 'Areas', icon: 'pi pi-fw pi-map', to: '/areas' },
        ].filter(Boolean),
    },
    {
        label: 'Usuarios y Seguridad',
        items: [
            hasPermission('ver usuarios') && { label: 'Gestión de Usuarios', icon: 'pi pi-fw pi-user-edit', to: '/usuario' },
            hasPermission('ver roles') && { label: 'Roles', icon: 'pi pi-fw pi-shield', to: '/roles' },
            hasPermission('ver empleados') && { label: 'Empleados', icon: 'pi pi-fw pi-id-card', to: '/empleados' },
            hasPermission('ver tipos_empleados') && { label: 'Tipo de empleados', icon: 'pi pi-fw pi-sitemap', to: '/tipo_empleados' },
            hasPermission('ver presentaciones') && { label: 'Presentaciones', icon: 'pi pi-fw pi-check-square', to: '/presentaciones' },
        ].filter(Boolean),
    },
].filter(section => section.items.length > 0));
</script>

<template>
    <ul class="layout-menu">
        <template v-for="(item, i) in model" :key="i">
            <app-menu-item :item="item" :index="i" />
        </template>
    </ul>
</template>

<style scoped lang="scss">
/* Estilos personalizados opcionales */
</style>
