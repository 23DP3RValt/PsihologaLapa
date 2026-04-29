<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/services/api'

const authUser = ref(JSON.parse(localStorage.getItem('authUser') || '{}'))
const router = useRouter()

const refreshAuth = () => {
  authUser.value = JSON.parse(localStorage.getItem('authUser') || '{}')
}

const isLoggedIn = computed(() => !!authUser.value?.token)
const isClient = computed(() => authUser.value?.role === 'client')
const isPsychologist = computed(() => authUser.value?.role === 'psychologist')

const logout = () => {
  const confirmed = window.confirm(
    'Vai tiešām vēlies izlogoties? Lūdzu apstiprini, ka piekrīti iziet no sistēmas.'
  )

  if (!confirmed) {
    return
  }

  localStorage.removeItem('authUser')
  refreshAuth()
  router.push('/')
}

onMounted(async () => {
  try {
    const response = await api.get('/health')
    console.log('Connected to Laravel!', response)
  } catch (error) {
    console.error('Connection failed:', error)
  }

  window.addEventListener('storage', refreshAuth)
  window.addEventListener('authUpdated', refreshAuth)
})

onBeforeUnmount(() => {
  window.removeEventListener('storage', refreshAuth)
  window.removeEventListener('authUpdated', refreshAuth)
})
</script>

<template>
  <nav class="nav">
    <div class="logo">
      <h2><b>Sporta Psihologija</b></h2>
    </div>
    <div class="navButton">
      <router-link to="/" custom v-slot="{ navigate, isActive }">
        <button @click="navigate" :class="{ active: isActive }">Sakums</button>
      </router-link>
      <router-link to="/rezervesana" custom v-slot="{ navigate, isActive }">
        <button @click="navigate" :class="{ active: isActive }">Rezervesana</button>
      </router-link>
      <a href="#info"><button>Lasamresursi</button></a>
      <a href="#parmani"><button>Par mani</button></a>
      <a href="#kontakti"><button>Kontakti</button></a>

      <template v-if="isClient">
        <router-link to="/dashboard" custom v-slot="{ navigate, isActive }">
          <button @click="navigate" :class="{ active: isActive }">Profils</button>
        </router-link>
      </template>

      <template v-else-if="isPsychologist">
        <router-link to="/psihologs" custom v-slot="{ navigate, isActive }">
          <button @click="navigate" :class="{ active: isActive }">Psihologa panelis</button>
        </router-link>
      </template>

      <template v-if="!isLoggedIn">
        <router-link to="/registresana" custom v-slot="{ navigate, isActive }">
          <button class="register-btn" @click="navigate" :class="{ active: isActive }">Registreties</button>
        </router-link>
        <router-link to="/register-psychologist" custom v-slot="{ navigate, isActive }">
          <button @click="navigate" :class="{ active: isActive }">Psihologa reģistrācija</button>
        </router-link>
        <router-link to="/login" custom v-slot="{ navigate, isActive }">
          <button @click="navigate" :class="{ active: isActive }">Pieslegties</button>
        </router-link>
      </template>

      <button v-if="isLoggedIn" @click="logout">Izlogoties</button>
    </div>
  </nav>

  <router-view />
</template>
<!--  -->
<style scoped>
.nav {
  height: 80px;
  width: 100vw;
  max-width: 100vw;
  background: white;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0 5%;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
  position: fixed;
  top: 0;
  left: 0;
  z-index: 1000;
  font-family: 'Quicksand', sans-serif;
}

.logo h2 {
  color: #003366;
  font-size: 1.5rem;
  font-weight: 700;
  margin: 0;
  letter-spacing: 0.5px;
}

.navButton {
  display: flex;
  gap: 20px;
  align-items: center;
}

.navButton a {
  text-decoration: none;
}

.navButton button {
  background: transparent;
  color: #333;
  border: none;
  border-radius: 30px;
  padding: 10px 20px;
  font-size: 0.95rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  font-family: 'Quicksand', sans-serif;
}

.navButton button:hover {
  background: rgba(0, 51, 102, 0.05);
  color: #003366;
}

.navButton button.active {
  background: rgba(0, 51, 102, 0.1);
  color: #003366;
  font-weight: 600;
}

.register-btn {
  background: linear-gradient(135deg, #ff9933 0%, #ff3300 100%) !important;
  color: white !important;
  font-weight: 600 !important;
  padding: 10px 24px !important;
  box-shadow: 0 4px 10px rgba(255, 51, 0, 0.2);
}

.register-btn:hover {
  background: linear-gradient(135deg, #ff3300 0%, #ff9933 100%) !important;
  transform: translateY(-2px);
  box-shadow: 0 6px 15px rgba(255, 51, 0, 0.3) !important;
  color: white !important;
}

@media (max-width: 768px) {
  .nav {
    height: auto;
    flex-direction: column;
    padding: 15px 5%;
  }

  .logo {
    margin-bottom: 10px;
  }

  .navButton {
    flex-wrap: wrap;
    justify-content: center;
    gap: 10px;
  }

  .navButton button {
    padding: 8px 16px;
    font-size: 0.9rem;
  }

  .register-btn {
    padding: 8px 20px !important;
  }
}

@media (max-width: 480px) {
  .logo h2 {
    font-size: 1.2rem;
  }

  .navButton button {
    padding: 6px 12px;
    font-size: 0.85rem;
  }

  .register-btn {
    padding: 6px 16px !important;
  }
}
</style>
