<template>
  <nav class="text-sm mb-4" aria-label="Breadcrumb">
    <ol class="list-reset flex text-gray-600">
      <li v-for="(item, index) in items" :key="index" class="flex items-center">
        <!-- Si no es el último, mostramos router-link -->
        <template v-if="index < items.length - 1">
          <router-link :to="item.to" class="hover:text-blue-600">
            {{ item.label }}
          </router-link>
          <span class="mx-2">/</span>
        </template>

        <!-- Último elemento: texto plano -->
        <template v-else>
          <span class="text-gray-800 font-semibold">{{ item.label }}</span>
        </template>
      </li>
    </ol>
  </nav>
</template>

<script setup>
defineProps({
  items: {
    type: Array,
    required: true,
    // Ejemplo: [{ label: 'Home', to: '/' }, { label: 'Admin', to: '/admin/dashboard' }, { label: 'Dashboard' }]
  }
});
</script>
