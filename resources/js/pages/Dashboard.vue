<script setup>
import AppLayout from '@/layout/AppLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import Password from './settings/Password.vue';
import Message from 'primevue/message';
import Button from 'primevue/button';

const page = usePage();
const mustReset = page.props.mustReset;
// Obtener el usuario autenticado
const user = page.props.auth.user;







</script>

<template>
  <Head title="Dashboard" />
  <div v-if="mustReset">
    <div>
      <Password />
    </div>
  </div>
  <AppLayout v-else>
    <div class="grid grid-cols-12 gap-8">
      <!-- Orders Card -->
      <div class="col-span-12 lg:col-span-6 xl:col-span-3">
        <div class="card mb-0">
          <div class="flex justify-between mb-4">
            <div>
              <span class="block text-muted-color font-medium mb-4">Pedidos</span>
              <div class="text-surface-900 dark:text-surface-0 font-medium text-xl">152</div>
            </div>
            <div class="flex items-center justify-center bg-blue-100 dark:bg-blue-400/10 rounded-border" style="width:2.5rem;height:2.5rem;">
              <i class="pi pi-shopping-cart text-blue-500 !text-xl"></i>
            </div>
          </div>
          <span class="text-primary font-medium">24 new </span><span class="text-muted-color">since last visit</span>
        </div>
      </div>

      <!-- Revenue Card -->
      <div class="col-span-12 lg:col-span-6 xl:col-span-3">
        <div class="card mb-0">
          <div class="flex justify-between mb-4">
            <div>
              <span class="block text-muted-color font-medium mb-4">Ingresos</span>
              <div class="text-surface-900 dark:text-surface-0 font-medium text-xl">$2,100</div>
            </div>
            <div class="flex items-center justify-center bg-orange-100 dark:bg-orange-400/10 rounded-border" style="width:2.5rem;height:2.5rem;">
              <i class="pi pi-dollar text-orange-500 !text-xl"></i>
            </div>
          </div>
          <span class="text-primary font-medium">%52+ </span><span class="text-muted-color">since last week</span>
        </div>
      </div>

      <!-- Customers Card -->
      <div class="col-span-12 lg:col-span-6 xl:col-span-3">
        <div class="card mb-0">
          <div class="flex justify-between mb-4">
            <div>
              <span class="block text-muted-color font-medium mb-4">Clientes</span>
              <div class="text-surface-900 dark:text-surface-0 font-medium text-xl">28,441</div>
            </div>
            <div class="flex items-center justify-center bg-cyan-100 dark:bg-cyan-400/10 rounded-border" style="width:2.5rem;height:2.5rem;">
              <i class="pi pi-users text-cyan-500 !text-xl"></i>
            </div>
          </div>
          <span class="text-primary font-medium">520 </span><span class="text-muted-color">newly registered</span>
        </div>
      </div>

      <!-- Comments Card -->
      <div class="col-span-12 lg:col-span-6 xl:col-span-3">
        <div class="card mb-0">
          <div class="flex justify-between mb-4">
            <div>
              <span class="block text-muted-color font-medium mb-4">Proveedores</span>
              <div class="text-surface-900 dark:text-surface-0 font-medium text-xl">152 Unread</div>
            </div>
            <div class="flex items-center justify-center bg-purple-100 dark:bg-purple-400/10 rounded-border" style="width:2.5rem;height:2.5rem;">
              <i class="pi pi-comment text-purple-500 !text-xl"></i>
            </div>
          </div>
          <span class="text-primary font-medium">85 </span><span class="text-muted-color">responded</span>
        </div>
      </div>

      <!-- Recent Sales Table -->
      <div class="col-span-12 xl:col-span-6">
        <div class="card">
          <div class="font-semibold text-xl mb-4">Recent Sales</div>
          <div class="p-datatable p-component">
            <div class="p-datatable-table-container" style="overflow: auto;">
              <table role="table" class="p-datatable-table">
                <thead class="p-datatable-thead" style="position: sticky;">
                  <tr role="row">
                    <th class="p-datatable-header-cell" style="width: 15%;">Image</th>
                    <th class="p-datatable-header-cell" style="width: 35%;">Name</th>
                    <th class="p-datatable-header-cell" style="width: 35%;">Price</th>
                    <th class="p-datatable-header-cell" style="width: 15%;">View</th>
                  </tr>
                </thead>
                <tbody class="p-datatable-tbody">
                  <tr>
                    <td><img src="https://primefaces.org/cdn/primevue/images/product/bamboo-watch.jpg" alt="bamboo-watch" width="50" class="shadow"></td>
                    <td>Bamboo Watch</td>
                    <td>$65.00</td>
                    <td><button class="p-button p-button-icon-only p-button-text"><span class="pi pi-search"></span></button></td>
                  </tr>
                  <tr>
                    <td><img src="https://primefaces.org/cdn/primevue/images/product/black-watch.jpg" alt="black-watch" width="50" class="shadow"></td>
                    <td>Black Watch</td>
                    <td>$72.00</td>
                    <td><button class="p-button p-button-icon-only p-button-text"><span class="pi pi-search"></span></button></td>
                  </tr>
                  <tr>
                    <td><img src="https://primefaces.org/cdn/primevue/images/product/blue-band.jpg" alt="blue-band" width="50" class="shadow"></td>
                    <td>Blue Band</td>
                    <td>$79.00</td>
                    <td><button class="p-button p-button-icon-only p-button-text"><span class="pi pi-search"></span></button></td>
                  </tr>
                  <tr>
                    <td><img src="https://primefaces.org/cdn/primevue/images/product/blue-t-shirt.jpg" alt="blue-t-shirt" width="50" class="shadow"></td>
                    <td>Blue T-Shirt</td>
                    <td>$29.00</td>
                    <td><button class="p-button p-button-icon-only p-button-text"><span class="pi pi-search"></span></button></td>
                  </tr>
                  <tr>
                    <td><img src="https://primefaces.org/cdn/primevue/images/product/bracelet.jpg" alt="bracelet" width="50" class="shadow"></td>
                    <td>Bracelet</td>
                    <td>$15.00</td>
                    <td><button class="p-button p-button-icon-only p-button-text"><span class="pi pi-search"></span></button></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Revenue Stream Chart -->
      <div class="col-span-12 xl:col-span-6">
        <div class="card">
          <div class="font-semibold text-xl mb-4">Revenue Stream</div>
          <div class="p-chart h-80">
            <canvas width="682" height="280" style="box-sizing: border-box; display: block; height: 280px; width: 682px;"></canvas>
          </div>
        </div>
      </div>

    </div>
  </AppLayout>
</template>