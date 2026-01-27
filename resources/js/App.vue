<template>
  <div class="flex h-screen">
   

    <!-- Contenedor principal -->
    <div class="flex-1 flex flex-col">
     

      <!-- Contenido dinámico de las rutas -->
      <main class="flex-1 p-6 bg-gray-100">
        <router-view />
      </main>
    </div>
  </div>
</template>

<script setup>

</script>
