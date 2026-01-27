<script setup>
import { onMounted, ref } from 'vue'
import api from '@/services/api'

const menuOpen = ref(false)

const toggleMenu = () => {
  menuOpen.value = !menuOpen.value
}

const closeMenu = () => {
  menuOpen.value = false
}

onMounted(async () => {
  try {
    const response = await api.get('/health')
    console.log('✅ Connected to Laravel!', response)
  } catch (error) {
    console.error('❌ Connection failed:', error)
  }
})
</script>

<template>
  <div class="app">
    <!-- Navigācija -->
    <nav class="navbar">
      <div class="container">
        <div class="nav-brand">
          <h1>🏆 Sporta Psiholoģija</h1>
        </div>
        <button class="menu-toggle" @click="toggleMenu">
          <span></span>
          <span></span>
          <span></span>
        </button>
        <ul class="nav-menu" :class="{ active: menuOpen }">
          <li><a href="#home" @click="closeMenu">Sākums</a></li>
          <li><a href="#about" @click="closeMenu">Par mums</a></li>
          <li><a href="#services" @click="closeMenu">Pakalpojumi</a></li>
          <li><a href="#booking" class="btn-primary" @click="closeMenu">Pieņemšanās</a></li>
        </ul>
      </div>
    </nav>

    <!-- Hero sekcija -->
    <section id="home" class="hero">
      <div class="hero-content">
        <h2>Uzlabo savu sporta sniegumu ar psiholoģijas palīdzību</h2>
        <p>Profesionālas konsultācijas un mentālā treninga programmas sportistu attīstībai</p>
        <a href="#booking" class="btn btn-large">Pierakstīties uz konsultāciju</a>
      </div>
    </section>
    <!-- Kontakti -->
    <footer class="footer">
      <div class="container">
        <p>&copy; 2024 Sporta Psiholoģija. Visas tiesības paturētas.</p>
      </div>
    </footer>
  </div>
</template>

<style scoped>
/* Font imports */
@font-face {
  font-family: 'Crimson Pro';
  src: url('/src/components/fonts/Crimson_Pro/static/CrimsonPro-Regular.ttf') format('truetype');
  font-weight: 400;
}

@font-face {
  font-family: 'Crimson Pro';
  src: url('/src/components/fonts/Crimson_Pro/static/CrimsonPro-Bold.ttf') format('truetype');
  font-weight: 700;
}

@font-face {
  font-family: 'Quicksand';
  src: url('/src/components/fonts/Quicksand/static/Quicksand-Regular.ttf') format('truetype');
  font-weight: 400;
}

@font-face {
  font-family: 'Quicksand';
  src: url('/src/components/fonts/Quicksand/static/Quicksand-SemiBold.ttf') format('truetype');
  font-weight: 600;
}

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

html,
body {
  width: 100%;
  height: 100%;

}

.app {
  font-family: 'Quicksand', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  line-height: 1.6;
  color: #333;
  width: 100%;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
}

/* Navigācija */
.navbar {
  background: linear-gradient(135deg, #0066cc 0%, #0099ff 100%);
  color: white;
  padding: 1rem 0;
  position: sticky;
  top: 0;
  z-index: 100;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.navbar .container {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.nav-brand h1 {
  font-family: 'Crimson Pro', serif;
  font-size: 1.8rem;
  font-weight: 700;
}

.nav-menu {
  display: flex;
  list-style: none;
  gap: 2rem;
}

.nav-menu a {
  color: white;
  text-decoration: none;
  transition: opacity 0.3s;
}

.nav-menu a:hover {
  opacity: 0.8;
}

/* Hamburger menu */
.menu-toggle {
  display: none;
  flex-direction: column;
  background: none;
  border: none;
  cursor: pointer;
  gap: 5px;
}

.menu-toggle span {
  width: 25px;
  height: 3px;
  background: white;
  border-radius: 2px;
  transition: all 0.3s;
}

.menu-toggle:hover span {
  background: #fbbf24;
}

/* Butons */
.btn {
  font-family: 'Quicksand', sans-serif;
  display: inline-block;
  padding: 0.7rem 1.5rem;
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  color: white;
  text-decoration: none;
  border-radius: 5px;
  border: none;
  cursor: pointer;
  transition: all 0.3s;
  font-size: 1rem;
  font-weight: 600;
}

.btn:hover {
  background: linear-gradient(135deg, #059669 0%, #047857 100%);
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(16, 185, 129, 0.4);
}

.btn-large {
  padding: 1rem 2rem;
  font-size: 1.1rem;
}

.btn-primary {
  background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
  border-radius: 25px;
  color: #000;
}

.btn-primary:hover {
  background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
}

/* Hero sekcija */
.hero {
  background: linear-gradient(135deg, #0066cc 0%, #00a3e0 50%, #22c55e 100%);
  color: white;
  padding: 100px 0;
  text-align: center;
  position: relative;
  overflow: hidden;
  width: 100%;
  min-height: 500px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.hero::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: 
    repeating-linear-gradient(45deg, transparent, transparent 100px, rgba(255,255,255,0.05) 100px, rgba(255,255,255,0.05) 200px),
    repeating-linear-gradient(-45deg, transparent, transparent 100px, rgba(255,255,255,0.08) 100px, rgba(255,255,255,0.08) 200px);
  pointer-events: none;
}

.hero::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  height: 60px;
  background: #ef4444;
  clip-path: polygon(0 30%, 100% 0%, 100% 100%, 0% 100%);
  z-index: 1;
}

.hero-content {
  position: relative;
  z-index: 2;
  max-width: 800px;
  margin: 0 auto;
  padding: 0 20px;
}

.hero-content h2 {
  font-family: 'Crimson Pro', serif;
  font-size: 2.5rem;
  font-weight: 700;
  margin-bottom: 1rem;
  text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
}

.hero-content p {
  font-family: 'Quicksand', sans-serif;
  font-size: 1.2rem;
  margin-bottom: 2rem;
  opacity: 0.95;
  text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
}

/* Sekcijas */
section {
  padding: 60px 0;
}

h2 {
  font-size: 2rem;
  margin-bottom: 2rem;
  text-align: center;
  color: #333;
}

/* Par mums */
.about {
  background: #f8f9fa;
}

.about p {
  font-size: 1.1rem;
  max-width: 800px;
  margin: 0 auto;
  text-align: center;
  color: #555;
}

/* Pakalpojumi */
.services-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 2rem;
}

.service-card {
  background: white;
  padding: 2rem;
  border-radius: 10px;
  box-shadow: 0 3px 10px rgba(0,0,0,0.1);
  transition: transform 0.3s, box-shadow 0.3s;
}

.service-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 20px rgba(0,0,0,0.15);
}

.service-card h3 {
  font-size: 1.3rem;
  margin-bottom: 1rem;
  color: #0066cc;
}

.service-card p {
  color: #666;
  line-height: 1.8;
}

/* Pierakstīšanās forma */
.booking {
  background: #f8f9fa;
}

.booking-form {
  max-width: 600px;
  margin: 0 auto;
  background: white;
  padding: 2rem;
  border-radius: 10px;
  box-shadow: 0 3px 10px rgba(0,0,0,0.1);
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 600;
  color: #333;
}

.form-group input,
.form-group textarea {
  width: 100%;
  padding: 0.8rem;
  border: 1px solid #ddd;
  border-radius: 5px;
  font-size: 1rem;
  font-family: inherit;
  transition: border-color 0.3s;
}

.form-group input:focus,
.form-group textarea:focus {
  outline: none;
  border-color: #0066cc;
  box-shadow: 0 0 5px rgba(0, 102, 204, 0.3);
}

/* Footer */
.footer {
  background: #333;
  color: white;
  text-align: center;
  padding: 2rem 0;
}

/* Responsive */
@media (max-width: 768px) {
  .nav-menu {
    gap: 1rem;
    font-size: 0.9rem;
  }

  .hero-content h2 {
    font-size: 1.8rem;
  }

  h2 {
    font-size: 1.5rem;
  }
}
</style>
