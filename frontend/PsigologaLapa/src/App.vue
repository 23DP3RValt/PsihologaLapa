<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '@/services/api'

const authUser = ref(JSON.parse(localStorage.getItem('authUser') || '{}'))
const router = useRouter()
const route = useRoute()
const menuOpen = ref(false)

const refreshAuth = () => {
  authUser.value = JSON.parse(localStorage.getItem('authUser') || '{}')
}

const isLoggedIn = computed(() => !!authUser.value?.token)
const isClient = computed(() => authUser.value?.role === 'client')
const isPsychologist = computed(() => authUser.value?.role === 'psychologist')

const roleLabel = computed(() => {
  if (isPsychologist.value) return 'Psihologs'
  if (isClient.value) return 'Klients'
  return 'Viesis'
})

const primaryNavItems = [
  { label: 'Sākums', to: { path: '/', hash: '#sakums' } },
  { label: 'Lasāmresursi', to: { path: '/', hash: '#info' } },
  { label: 'Par mani', to: { path: '/', hash: '#parmani' } },
  { label: 'Kontakti', to: { path: '/', hash: '#kontakti' } },
  { label: 'Rezervēšana', to: '/rezervesana' }
]

const roleNavItems = computed(() => {
  if (isClient.value) {
    return [{ label: 'Profils', to: '/dashboard', variant: 'accent' }]
  }

  if (isPsychologist.value) {
    return [
      { label: 'Panelis', to: '/psihologs', variant: 'accent' },
      { label: 'Klienti', to: '/psihologs/clients' }
    ]
  }

  return [
    { label: 'Reģistrēties', to: '/registresana', variant: 'accent' },
    { label: 'Psihologiem', to: '/register-psychologist' },
    { label: 'Pieslēgties', to: '/login' }
  ]
})

const closeMenu = () => {
  menuOpen.value = false
}

const toggleMenu = () => {
  menuOpen.value = !menuOpen.value
}

const logout = () => {
  const confirmed = window.confirm(
    'Vai tiešām vēlies izlogoties? Lūdzu apstiprini, ka piekrīti iziet no sistēmas.'
  )

  if (!confirmed) {
    return
  }

  localStorage.removeItem('authUser')
  refreshAuth()
  closeMenu()
  router.push('/')
}

watch(() => route.fullPath, closeMenu)

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
  <nav class="nav" aria-label="Galvenā navigācija">
    <div class="nav-brand">
      <router-link to="/" class="logo" aria-label="Sporta Psihologija sākumlapa">
        <span class="logo-mark">SP</span>
        <span class="logo-copy">
          <strong>Sporta Psihologija</strong>
          <small>{{ roleLabel }}</small>
        </span>
      </router-link>
    </div>

    <div class="nav-content">
      <div class="navButton nav-primary">
        <router-link
          v-for="item in primaryNavItems"
          :key="item.label"
          :to="item.to"
          custom
          v-slot="{ navigate, isActive }"
        >
          <button type="button" @click="navigate" :class="{ active: isActive }">
            {{ item.label }}
          </button>
        </router-link>
      </div>

      <div class="navButton nav-actions">
        <router-link
          v-for="item in roleNavItems"
          :key="item.label"
          :to="item.to"
          custom
          v-slot="{ navigate, isActive }"
        >
          <button
            type="button"
            @click="navigate"
            :class="[{ active: isActive }, item.variant === 'accent' ? 'register-btn' : '']"
          >
            {{ item.label }}
          </button>
        </router-link>

        <button v-if="isLoggedIn" type="button" class="logout-btn" @click="logout">
          Izlogoties
        </button>
      </div>
    </div>

    <button
      type="button"
      class="menu-toggle"
      :class="{ open: menuOpen }"
      :aria-expanded="menuOpen"
      aria-controls="mobile-navigation"
      aria-label="Atvert navigaciju"
      @click="toggleMenu"
    >
      <span></span>
      <span></span>
      <span></span>
    </button>
  </nav>

  <div v-if="menuOpen" class="nav-backdrop" @click="closeMenu"></div>
  <aside id="mobile-navigation" class="mobile-menu" :class="{ open: menuOpen }" aria-label="Mobila navigacija">
    <div class="mobile-menu-header">
      <span>Izvelne</span>
      <button type="button" aria-label="Aizvert navigaciju" @click="closeMenu">x</button>
    </div>

    <div class="mobile-menu-section">
      <router-link
        v-for="item in primaryNavItems"
        :key="item.label"
        :to="item.to"
        custom
        v-slot="{ navigate, isActive }"
      >
        <button type="button" @click="navigate(); closeMenu()" :class="{ active: isActive }">
          {{ item.label }}
        </button>
      </router-link>
    </div>

    <div class="mobile-menu-section mobile-actions">
      <router-link
        v-for="item in roleNavItems"
        :key="item.label"
        :to="item.to"
        custom
        v-slot="{ navigate, isActive }"
      >
        <button
          type="button"
          @click="navigate(); closeMenu()"
          :class="[{ active: isActive }, item.variant === 'accent' ? 'register-btn' : '']"
        >
          {{ item.label }}
        </button>
      </router-link>

      <button v-if="isLoggedIn" type="button" class="logout-btn" @click="logout">
        Izlogoties
      </button>
    </div>
  </aside>

  <router-view />
</template>

<style scoped>
.nav {
  min-height: 84px;
  width: 100%;
  max-width: 100%;
  background: rgba(255, 255, 255, 0.94);
  backdrop-filter: blur(18px);
  border-bottom: 1px solid rgba(34, 85, 179, 0.12);
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  align-items: center;
  gap: 28px;
  padding: 12px clamp(20px, 5vw, 72px);
  box-shadow: 0 14px 40px rgba(24, 43, 84, 0.08);
  position: fixed;
  top: 0;
  left: 0;
  z-index: 1000;
  font-family: 'Quicksand', sans-serif;
  overflow: hidden;
}

.nav-brand {
  flex: 0 0 auto;
}

.logo {
  display: inline-flex;
  align-items: center;
  gap: 12px;
  color: #17264d;
  text-decoration: none;
  margin-bottom: 0;
}

.logo-mark {
  width: 46px;
  height: 46px;
  border-radius: 14px;
  background:
    linear-gradient(135deg, rgba(34, 85, 179, 0.96), rgba(30, 196, 242, 0.9)),
    var(--color-primary-start);
  color: #fff;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  letter-spacing: 0;
  box-shadow: 0 10px 24px rgba(34, 85, 179, 0.22);
}

.logo-copy {
  display: grid;
  gap: 2px;
}

.logo-copy strong {
  color: var(--color-primary-start);
  font-size: 1.08rem;
  font-weight: 700;
  line-height: 1.1;
  letter-spacing: 0;
}

.logo-copy small {
  color: #6d7890;
  font-size: 0.78rem;
  font-weight: 600;
  line-height: 1;
}

.nav-content {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  gap: 14px;
  min-width: 0;
  flex: 1;
}

.navButton {
  display: flex;
  flex-wrap: nowrap;
  align-items: center;
  gap: 6px;
  min-width: 0;
}

.nav-primary {
  flex: 1 1 auto;
  padding: 0;
  overflow-x: auto;
  scrollbar-width: none;
}

.nav-primary::-webkit-scrollbar,
.nav-actions::-webkit-scrollbar {
  display: none;
}

.nav-actions {
  max-width: 44vw;
  flex-shrink: 0;
  overflow-x: auto;
  scrollbar-width: none;
}

.navButton a {
  text-decoration: none;
}

.navButton button {
  background: #f4f7fb;
  color: #32405c;
  border: 1px solid rgba(34, 85, 179, 0.08);
  border-radius: 999px;
  min-height: 40px;
  padding: 10px 16px;
  font-size: 0.93rem;
  font-weight: 700;
  line-height: 1;
  cursor: pointer;
  transition: all 0.2s ease;
  font-family: 'Quicksand', sans-serif;
  white-space: nowrap;
}

.navButton button:hover {
  background: rgba(34, 85, 179, 0.1);
  color: var(--color-primary-start);
}

.navButton button.active {
  background: #fff;
  color: var(--color-primary-start);
  box-shadow: 0 8px 20px rgba(24, 43, 84, 0.09);
}

.register-btn {
  background: linear-gradient(135deg, #dd4125 0%, #b22f1e 100%) !important;
  color: white !important;
  padding: 10px 18px !important;
  box-shadow: 0 10px 22px rgba(221, 65, 37, 0.24);
}

.register-btn:hover {
  background: linear-gradient(135deg, #b22f1e 0%, #dd4125 100%) !important;
  transform: translateY(-1px);
  box-shadow: 0 14px 26px rgba(221, 65, 37, 0.3) !important;
}

.logout-btn {
  color: #9f2f22 !important;
}

.logout-btn:hover {
  background: rgba(221, 65, 37, 0.1) !important;
  color: #8c2418 !important;
}

.menu-toggle {
  display: none;
  width: 44px;
  height: 44px;
  flex: 0 0 auto;
  border: 1px solid rgba(34, 85, 179, 0.14);
  border-radius: 12px;
  background: #f4f7fb;
  cursor: pointer;
  padding: 10px;
}

.menu-toggle span {
  display: block;
  width: 100%;
  height: 2px;
  border-radius: 999px;
  background: var(--color-primary-start);
  transition: transform 0.2s ease, opacity 0.2s ease;
}

.menu-toggle span + span {
  margin-top: 6px;
}

.menu-toggle.open span:nth-child(1) {
  transform: translateY(8px) rotate(45deg);
}

.menu-toggle.open span:nth-child(2) {
  opacity: 0;
}

.menu-toggle.open span:nth-child(3) {
  transform: translateY(-8px) rotate(-45deg);
}

.nav-backdrop {
  position: fixed;
  inset: 0;
  z-index: 1001;
  background: rgba(16, 36, 62, 0.32);
}

.mobile-menu {
  position: fixed;
  top: 0;
  right: 0;
  z-index: 1002;
  width: min(86vw, 340px);
  height: 100vh;
  padding: 18px;
  background: rgba(255, 255, 255, 0.98);
  box-shadow: -20px 0 50px rgba(16, 36, 62, 0.18);
  transform: translateX(100%);
  transition: transform 0.24s ease;
  display: grid;
  align-content: start;
  gap: 18px;
  overflow-y: auto;
}

.mobile-menu.open {
  transform: translateX(0);
}

.mobile-menu-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  color: #17264d;
  font-size: 0.86rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0;
}

.mobile-menu-header button {
  width: 38px;
  height: 38px;
  border: none;
  border-radius: 12px;
  background: #eef5ff;
  color: var(--color-primary-start);
  font-size: 1.35rem;
  line-height: 1;
  cursor: pointer;
}

.mobile-menu-section {
  display: grid;
  gap: 10px;
}

.mobile-menu-section a {
  text-decoration: none;
}

.mobile-menu-section button {
  width: 100%;
  min-height: 46px;
  padding: 12px 14px;
  border: 1px solid rgba(34, 85, 179, 0.1);
  border-radius: 12px;
  background: #f4f7fb;
  color: #32405c;
  font: inherit;
  font-size: 0.95rem;
  font-weight: 700;
  text-align: left;
  cursor: pointer;
}

.mobile-menu-section button.active {
  background: #eef5ff;
  color: var(--color-primary-start);
}

.mobile-actions {
  padding-top: 14px;
  border-top: 1px solid rgba(34, 85, 179, 0.12);
}

@media (max-width: 1100px) {
  .nav {
    flex-direction: row;
    gap: 16px;
    padding: 10px 24px;
  }

  .nav-content {
    gap: 10px;
  }

  .nav-primary {
    flex: 1 1 auto;
  }
}

@media (max-width: 768px) {
  .nav {
    min-height: 72px;
    gap: 8px;
    padding: 8px 12px;
  }

  .nav-content {
    display: none;
  }

  .menu-toggle {
    display: block;
  }

  .logo-mark {
    width: 42px;
    height: 42px;
    border-radius: 12px;
  }

  .logo-copy strong {
    font-size: 1rem;
  }

  .logo-copy strong {
    max-width: 190px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }
}

@media (max-width: 480px) {
  .nav {
    min-height: 68px;
    padding: 8px 10px;
    gap: 8px;
  }

  .logo {
    gap: 10px;
  }

  .logo-copy {
    display: grid;
  }

  .logo-copy strong {
    max-width: 140px;
    font-size: 0.92rem;
  }

  .logo-copy small {
    font-size: 0.7rem;
  }

  .menu-toggle {
    width: 40px;
    height: 40px;
    padding: 9px;
  }
}

@media (max-width: 380px) {
  .logo-mark {
    width: 38px;
    height: 38px;
    border-radius: 11px;
    font-size: 0.9rem;
  }

  .logo-copy strong {
    max-width: 118px;
  }
}
</style>
