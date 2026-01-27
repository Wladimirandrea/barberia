<template>
  <div v-if="open" class="modal-overlay">
    <div class="modal-content">

      <h2 class="modal-title">Confirmar horario</h2>

      <div class="modal-body">
        <p><strong>Día:</strong> {{ initialData.date }}</p>
        <p><strong>Inicio:</strong> {{ initialData.start_time }}</p>
        <p><strong>Fin:</strong> {{ initialData.end_time }}</p>
      </div>

      <div class="modal-actions">
        <button class="btn-cancel" @click="$emit('close')">Cancelar</button>
        <button class="btn-save" @click="guardar">Guardar</button>
      </div>

    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  open: Boolean,
  initialData: Object
})

const emit = defineEmits(['close', 'save'])

const guardar = () => {
  emit('save', props.initialData)
}
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.4);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 9;
}

.modal-content {
  background: white;
  padding: 1.5rem;
  border-radius: 10px;
  width: 350px;
}

.modal-title {
  font-size: 1.3rem;
  margin-bottom: 1rem;
}

.modal-body p {
  margin: 0.3rem 0;
}

.modal-actions {
  margin-top: 1.5rem;
  display: flex;
  justify-content: flex-end;
  gap: 10px;
}

.btn-cancel {
  background: #e5e7eb;
  padding: 0.5rem 1rem;
  border-radius: 6px;
}

.btn-save {
  background: #22c55e;
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 6px;
}
</style>
