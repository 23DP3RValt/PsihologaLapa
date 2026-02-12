<script setup>
// import HelloWorld from './components/HelloWorld.vue'
// import TheWelcome from './components/TheWelcome.vue'
import { onMounted } from 'vue'
import api from '@/services/api'

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
  <nav class="nav">
    <div class="logo">
      <h2>Sporta Psiholoģija</h2>
    </div>
    <div class="navButton">
      <a href="#home"><button>Sākums</button></a>
      <a href="#about"><button>Informācija</button></a>
      <a href="#services"><button>Par mani</button></a>
      <a href="#contact"><button>Kontakti</button></a>
    </div>
  </nav>
</template>

<style scoped>
.nav {
  height: 80px;
  width: 100vw;
  max-width: 100vw;
  background: linear-gradient(135deg, #0066cc 0%, #0099ff 100%);
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0 5%;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  position: fixed;
  top: 0;
  left: 0;
  z-index: 1000;
}

.logo h2 {
  color: white;
  font-size: 1.5rem;
  font-weight: 600;
  margin: 0;
  text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
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
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  color: white;
  border: none;
  border-radius: 25px;
  padding: 10px 24px;
  font-size: 1rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.navButton button:hover {
  background: linear-gradient(135deg, #059669 0%, #047857 100%);
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(16, 185, 129, 0.4);
}

/* Responsive */
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
}

@media (max-width: 480px) {
  .logo h2 {
    font-size: 1.2rem;
  }
  
  .navButton button {
    padding: 6px 12px;
    font-size: 0.85rem;
  }
}
</style>