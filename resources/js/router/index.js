// resources/js/router/index.js

import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'

import Welcome from '../views/Welcome.vue'
import Login from '../views/Login.vue'
import Register from '../views/Register.vue'
import UserDashboard from '../views/user/UserDashboard.vue'
import ChangePassword from '../views/user/ChangePassword.vue'
import AdminDashboard from '../views/admin/AdminDashboard.vue'
import AdminUsers from '../views/admin/AdminUsers.vue'
import AdminUserCreate from '../views/admin/AdminUserCreate.vue'
import AdminUserEdit from '../views/admin/AdminUserEdit.vue'
import ScheduleView from '../views/admin/ScheduleView.vue'
import DaysOffView from '../views/admin/DaysOffView.vue'
import UserScheduleView from '../views/user/UserScheduleView.vue'
import UserAppointmentsView from '../views/user/UserAppointmentsView.vue'
import AdminAppointmentsView from '@/views/admin/AdminAppointmentsView.vue'


const routes = [
  { path: '/', component: Welcome }, // página principal simple
  { path: '/login', component: Login },
  { path: '/register', component: Register },
  { path: '/user/dashboard', component: UserDashboard, meta: { requiresAuth: true, role: ['user', 'usuario'] } },
  { path: '/user/change-password', component: ChangePassword, meta: { requiresAuth: true, role: ['user','usuario'] } }, // 👈 nueva ruta
  { path: '/user/schedule', component: UserScheduleView, meta: { requiresAuth: true, role: ['user','usuario'] } },
  { path: '/user/appointments', component: UserAppointmentsView, meta: { requiresAuth: true, role: ['user','usuario'] } },

  { path: '/admin/dashboard', component: AdminDashboard, meta: { requiresAuth: true, role: 'admin' } },
  { path: '/admin/users', component: AdminUsers, meta: { requiresAuth: true, role: 'admin' } },
  { path: '/admin/users/create', component: AdminUserCreate, meta: { requiresAuth: true, role: 'admin' } },
  { path: '/admin/users/:id/edit', component: AdminUserEdit, meta: { requiresAuth: true, role: 'admin' } },
  { path: '/admin/schedules', component: ScheduleView, meta: { requiresAuth: true, role: 'admin' } },
  { path: '/admin/days-off', component: DaysOffView, meta: { requiresAuth: true, role: 'admin' } },

  { path: '/admin/appointments', component: AdminAppointmentsView, meta: { requiresAuth: true, role: 'admin' } },
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// ✅ Guard global para autenticación y roles
router.beforeEach((to, from, next) => {
  const auth = useAuthStore()

  const publicPaths = ['/', '/login', '/register']
  if (publicPaths.includes(to.path)) return next()

  if (to.meta?.requiresAuth) {
    if (!auth.token) {
      return next('/login')
    }

    const currentRole = auth.role
    if (to.meta.role) {
      // 👇 si meta.role es array, comprobamos inclusión
      if (Array.isArray(to.meta.role)) {
        if (!to.meta.role.includes(currentRole)) {
          if (currentRole === 'admin') return next('/admin/dashboard')
          if (currentRole === 'user' || currentRole === 'usuario') return next('/user/dashboard')
          return next('/login')
        }
      } else if (to.meta.role !== currentRole) {
        if (currentRole === 'admin') return next('/admin/dashboard')
        if (currentRole === 'user' || currentRole === 'usuario') return next('/user/dashboard')
        return next('/login')
      }
    }
  }

  next()
})

export default router
