<template>
  <div>
    <Navbar />

    <div class="p-6">
      <Breadcrumb :items="[
        { label: 'Dashboard', to: '/user/dashboard' },
        { label: 'Reservar horario' }
      ]" />
    </div>

    <div class="p-6">
      <UserScheduleCalendar />
    </div>
  </div>
</template>

<script setup>
import Navbar from '@/componentes/Navbar.vue'
import Breadcrumb from '@/componentes/Breadcrumb.vue'
import UserScheduleCalendar from '@/componentes/UserScheduleCalendar.vue'
</script>

