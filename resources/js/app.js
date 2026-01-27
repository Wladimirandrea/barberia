import './bootstrap';
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import router from './router';

import App from './App.vue';

import flatpickr from 'flatpickr'
import 'flatpickr/dist/flatpickr.min.css'

import Vue3Toastify from 'vue3-toastify'
import 'vue3-toastify/dist/index.css'

import { useAdminAppointmentsStore } from '@/stores/adminAppointments'

const app = createApp(App);

app.use(createPinia());
app.use(router);

app.use(Vue3Toastify, {
  autoClose: 4000,
  position: "top-right"
});

// 🟩 Exponer store global para debugging
const adminStore = useAdminAppointmentsStore()
window.adminStore = adminStore

app.mount('#app');
