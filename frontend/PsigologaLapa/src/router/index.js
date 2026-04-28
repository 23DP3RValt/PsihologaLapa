import { createRouter, createWebHistory } from 'vue-router'
import HomePage from '@/pages/HomePage.vue'
import ReservationPage from '@/pages/ReservationPage.vue'
import RegisterPage from '@/pages/RegisterPage.vue'
import RegisterPsychologistPage from '@/pages/RegisterPsychologistPage.vue'

const routes = [
  {
    path: '/',
    name: 'home',
    component: HomePage
  },
  {
    path: '/rezervesana',
    name: 'rezervesana',
    component: ReservationPage
  },
  {
    path: '/registresana',
    name: 'registresana',
    component: RegisterPage
  },
  {
    path: '/register-psychologist',
    name: 'register-psychologist',
    component: RegisterPsychologistPage
  },
  {
    path: '/login',
    name: 'login',
    component: () => import('../pages/Login.vue')
  },
  {
    path: '/dashboard',
    name: 'dashboard',
    component: () => import('../pages/Dashboard.vue')
  },
  {
    path: '/psihologs',
    name: 'psychologist-dashboard',
    component: () => import('../pages/PsychologistDashboard.vue')
  },
  {
    path: '/psihologs/clients',
    name: 'psychologist-clients',
    component: () => import('../pages/PsychologistClients.vue')
  }
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition
    }

    return { top: 0 }
  }
})

export default router
