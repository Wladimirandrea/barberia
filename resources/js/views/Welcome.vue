<template>
  <div class="flex flex-col items-center justify-center h-screen bg-gray-100">
    <h1 class="text-3xl font-bold mb-6">Página Principal</h1>

    <div class="space-x-4">
      <router-link
        to="/login"
        class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
      >
        Login
      </router-link>

      <router-link
        to="/register"
        class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600"
      >
        Register
      </router-link>
    </div>
  </div>
</template>
