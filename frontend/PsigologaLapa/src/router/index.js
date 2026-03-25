import { createRouter, createWebHistory } from 'vue-router'
import HomePage from '@/pages/HomePage.vue'
import ReservationPage from '@/pages/ReservationPage.vue'
import RegisterPage from '@/pages/RegisterPage.vue'
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
  }
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition
    } else {
      return { top: 0 }
    }
  }
})

export default router
