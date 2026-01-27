<template>
  <aside class="w-64 h-screen bg-gray-800 text-white flex flex-col">
    <div class="p-4 text-xl font-bold border-b border-gray-700">
      Panel
    </div>

    <nav class="flex-1 p-4 space-y-2">
      <!-- Home -->
      <router-link
        to="/"
        class="block py-2 px-3 rounded hover:bg-gray-700"
      >
        Home
      </router-link>

      <!-- Dropdown Usuarios -->
      <div>
        <button
          @click="toggleDropdown"
          class="w-full flex justify-between items-center py-2 px-3 rounded hover:bg-gray-700"
        >
          <span>Usuarios</span>
          <svg
            :class="{ 'rotate-180': dropdownOpen }"
            class="w-4 h-4 transition-transform"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M19 9l-7 7-7-7" />
          </svg>
        </button>

        <div v-if="dropdownOpen" class="ml-4 mt-2 space-y-2">
          <router-link
            to="/register"
            class="block py-2 px-3 rounded hover:bg-gray-700"
          >
            Register
          </router-link>
          <router-link
            to="/login"
            class="block py-2 px-3 rounded hover:bg-gray-700"
          >
            Login
          </router-link>
        </div>
      </div>

      <!-- Botón Logout -->
      <button
        @click="logout"
        class="w-full text-left py-2 px-3 rounded hover:bg-red-600 mt-4"
      >
        Logout
      </button>
    </nav>
  </aside>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();
const dropdownOpen = ref(false);

const toggleDropdown = () => {
  dropdownOpen.value = !dropdownOpen.value;
};

const logout = async () => {
  const token = localStorage.getItem('token');

  if (!token) {
    router.push('/login');
    return;
  }

  const res = await fetch('/api/logout', {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json'
    }
  });

  if (res.ok) {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    router.push('/login');
  } else {
    alert('Error al cerrar sesión');
  }
};
</script>
