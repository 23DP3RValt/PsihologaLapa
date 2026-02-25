<script setup>
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
      <h2><b>Sporta Psiholoģija</b></h2>
    </div>
    <div class="navButton">
      <router-link to="/" custom v-slot="{ navigate, isActive }">
        <button @click="navigate" :class="{ active: isActive }">Sākums</button>
      </router-link>
      <router-link to="/rezervesana" custom v-slot="{ navigate, isActive }">
        <button @click="navigate" :class="{ active: isActive }">Rezervēšana</button>
      </router-link>
      <a href="#info"><button>Lasāmresursi</button></a>
      <a href="#parmani"><button>Par mani</button></a>
      <a href="#kontakti"><button>Kontakti</button></a>
      <button class="register-btn">Reģistrēties</button>
    </div>
  </nav>
  
  <router-view />
</template>

<style scoped>
/* Navigation */
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
  font-family: "Quicksand", sans-serif;
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
  font-family: "Quicksand", sans-serif;
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
  background: linear-gradient(135deg, #FF9933 0%, #FF3300 100%) !important;
  color: white !important;
  font-weight: 600 !important;
  padding: 10px 24px !important;
  box-shadow: 0 4px 10px rgba(255, 51, 0, 0.2);
}

.register-btn:hover {
  background: linear-gradient(135deg, #FF3300 0%, #FF9933 100%) !important;
  transform: translateY(-2px);
  box-shadow: 0 6px 15px rgba(255, 51, 0, 0.3) !important;
  color: white !important;
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
    
    <!-- <div id="sakums" class="sakums">
      <div class="hero-content">
        <div class="hero-text">
          <h1>KĀ KOGNITĪVIE PROCESI SAISTĪTI<br>AR FIZISKO VEIKTSPĒJU?</h1>
          <h2>LATVIJĀ NEBIJIS PĒTĪJUMS<br>SPORTA PSIHOLOĢIJĀ</h2>
          <a href="#registrs" class="hero-btn">PIESAKIES DALĪBAI PĒTĪJUMĀ</a>
        </div>
      </div>
    </div>
    
    <div id="rezervet" class="rezervet">
      <Calendar />
    </div>
    <div id="info" class="info">
      <EventsList />
    </div>
    <div id="parmani" class="parmani">
      
    </div>
    <div id="kontakti" class="kontakti">
      <form action="Controler.php">
        <label for="mam">Label <br></label>
        <input type="text" name="user" placeholder="Username"> <br>
        <input type="password" name="password" placeholder="Password"> <br>
        <input type="submit" name="submit">
        <a href="/side.html"><button type="button">dd</button></a>
      </form>
    </div>
  </body>
</template> -->

