<template>
  <div v-if="open" class="modal-overlay">
    <div class="modal-content">

      <h2 class="modal-title">Confirmar Reserva</h2>

      <div class="modal-body">
        <p><strong>Fecha:</strong> {{ formatDate(slotData.date) }}</p>
        <p><strong>Hora:</strong> {{ slotData.start_time }} - {{ slotData.end_time }}</p>
      </div>

      <div class="modal-actions">
        <button class="btn-cancel" @click="$emit('close')">Cancelar</button>
        <button class="btn-confirm" @click="$emit('confirm')">Confirmar</button>
      </div>

    </div>
  </div>
</template>

<script setup>
import { defineProps } from 'vue'

const props = defineProps({
  open: Boolean,
  slotData: {
    type: Object,
    default: () => ({})
  }
})

/**
 * Formatear fecha a formato legible
 */
const formatDate = (date) => {
  if (!date) return ''

  // Evitar que JS convierta la fecha a UTC
  const [year, month, day] = date.split('-')

  const fecha = new Date(year, month - 1, day)

  return fecha.toLocaleDateString('es-ES', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}


</script>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.55);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 2000;
}

.modal-content {
  background: white;
  padding: 2rem;
  border-radius: 1rem;
  width: 380px;
  box-shadow: 0 10px 25px rgba(0,0,0,0.2);
  animation: fadeIn 0.2s ease-out;
}

.modal-title {
  font-size: 1.4rem;
  font-weight: bold;
  margin-bottom: 1rem;
  text-align: center;
}

.modal-body {
  margin-bottom: 1.5rem;
  font-size: 1.1rem;
}

.modal-actions {
  display: flex;
  justify-content: space-between;
}

.btn-cancel {
  background: #e5e7eb;
  padding: 0.6rem 1.2rem;
  border-radius: 0.5rem;
  border: none;
  cursor: pointer;
  font-weight: 600;
}

.btn-confirm {
  background: #22c55e;
  color: white;
  padding: 0.6rem 1.2rem;
  border-radius: 0.5rem;
  border: none;
  cursor: pointer;
  font-weight: 600;
}

.btn-confirm:hover {
  background: #16a34a;
}

@keyframes fadeIn {
  from { opacity: 0; transform: scale(0.95); }
  to { opacity: 1; transform: scale(1); }
}
</style>
