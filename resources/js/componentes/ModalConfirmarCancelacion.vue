<template>
  <div v-if="open" class="modal-overlay">
    <div class="modal-content">

      <h2 class="modal-title">¿Cancelar reserva?</h2>

      <div class="modal-body">
        <p>¿Estás seguro de que deseas cancelar esta reserva?</p>
      </div>

      <div class="modal-actions">
        <button class="btn-cancel" @click="$emit('close')">No, volver</button>
        <button class="btn-confirm" @click="$emit('confirm')">Sí, cancelar</button>
      </div>

    </div>
  </div>
</template>

<script setup>
defineProps({
  open: Boolean
})
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 50;
}
.modal-content {
  background: white;
  padding: 1.5rem;
  border-radius: 10px;
  width: 350px;
}
.modal-title {
  font-size: 1.3rem;
  font-weight: bold;
  margin-bottom: 1rem;
}
.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  margin-top: 1.5rem;
}
.btn-cancel {
  background: #e5e7eb;
  padding: 0.5rem 1rem;
  border-radius: 6px;
}
.btn-confirm {
  background: #dc2626;
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 6px;
}
</style>
