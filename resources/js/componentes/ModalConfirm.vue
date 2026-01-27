<template>
  <div
    v-if="show"
    class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50"
  >
    <div class="bg-white rounded-lg shadow-lg p-6 w-80 animate-fadeIn">
      <h3 class="text-lg font-bold mb-4">{{ title }}</h3>
      <p class="mb-6">{{ message }}</p>

      <div class="flex justify-end gap-3">
        <button
          class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400"
          @click="$emit('cancel')"
        >
          No
        </button>

        <button
          class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700"
          @click="$emit('confirm')"
        >
          Sí, cancelar
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  show: {
    type: Boolean,
    required: true
  },
  title: {
    type: String,
    default: 'Confirmar acción'
  },
  message: {
    type: String,
    default: '¿Estás seguro?'
  }
})
</script>

<style scoped>
.animate-fadeIn {
  animation: fadeIn .2s ease-out;
}
@keyframes fadeIn {
  from { opacity: 0; transform: scale(.95); }
  to { opacity: 1; transform: scale(1); }
}
</style>
