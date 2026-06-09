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
  <div>
    <div id="sakums" class="sakums">
      <div class="hero-content">
        <div class="hero-text">
          <h1>KĀ KOGNITĪVIE PROCESI SAISTĪTI<br>AR FIZISKO VEIKTSPĒJU?</h1>
          <h2>LATVIJĀ NEBIJIS PĒTĪJUMS<br>SPORTA PSIHOLOĢIJĀ</h2>
          <router-link to="/rezervesana" class="hero-btn">PIESAKIES DALĪBAI PĒTĪJUMĀ</router-link>
        </div>
      </div>
    </div>
    
    <div id="info" class="info">
      <h3>Lasāmresursi</h3>
      <p>Informatīvā informācija par sporta psiholoģiju</p>
    </div>
    
    <div id="parmani" class="parmani">
      <h3>Par mani</h3>
      <p>Informācija par speciālistu</p>
    </div>
    
    <div id="kontakti" class="kontakti">
      <h3>Kontakti</h3>
      <form action="Controler.php">
        <label for="mam">Label <br></label>
        <input type="text" name="user" placeholder="Username"> <br>
        <input type="password" name="password" placeholder="Password"> <br>
        <input type="submit" name="submit">
        <a href="/side.html"><button type="button">dd</button></a>
      </form>
    </div>
  </div>
</template>

<style scoped>
/* Hero Section - Sakums */
.sakums {
  min-height: calc(100vh - 80px);
  width: 100%;
  background-image: url(C:\Users\Reinis\Documents\PsihologaLapa\frontend\PsigologaLapa\src\components\icons\SmuksHeroFons2.png);
  background-position: center;
  background-size:cover;
  background-repeat: no-repeat;
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-top: 80px;
  padding-top: 0;
}

.hero-content {
  position: relative;
  z-index: 2;
  width: 100%;
  max-width: 1200px;
  padding: 0 clamp(16px, 5vw, 32px);
  margin: 0 auto;
}

.hero-text {
  text-align: center;
  color: var(--color-primary-start);
  max-width: 900px;
  margin: 0 auto;
}

.hero-text h1 {
  font-size: clamp(2rem, 5vw, 3.2rem);
  font-weight: 700;
  margin-bottom: 1.5rem;
  letter-spacing: 0;
  line-height: 1.3;
  font-family: 'Crimson Pro', serif;
  color: var(--color-heading);
  text-transform: uppercase;
}

.hero-text h2 {
  font-size: clamp(1.35rem, 3.8vw, 2.2rem);
  font-weight: 600;
  margin-bottom: 2.5rem;
  color: #DD4125;
  font-family: 'Crimson Pro', serif;
  line-height: 1.3;
  letter-spacing: 0;
  text-transform: uppercase;
}

.hero-btn {
  display: inline-block;
  background: linear-gradient(135deg, #DD4125 0%, #B22F1E 100%);
  color: white;
  padding: 18px 50px;
  font-size: 1.2rem;
  font-weight: 700;
  text-decoration: none;
  border-radius: 50px;
  box-shadow: 0 10px 25px rgba(255, 51, 0, 0.3);
  transition: all 0.2s ease;
  border: none;
  cursor: pointer;
  font-family: 'Quicksand', sans-serif;
  letter-spacing: 0;
  text-transform: uppercase;
  max-width: 100%;
  text-align: center;
  white-space: normal;
}

.hero-btn:hover {
  background: linear-gradient(135deg, #B22F1E 0%, #DD4125 100%);
  transform: translateY(-2px);
  box-shadow: 0 15px 30px rgba(255, 51, 0, 0.4);
}

/* Other sections */
.info,
.parmani,
.kontakti {
  min-height: 100vh;
  width: 100%;
  padding: 80px 20px 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 2rem;
  font-weight: 600;
  flex-direction: column;
}

.info {
  background: linear-gradient(135deg, #87CC27 0%, #BDD823 100%);
}

.parmani {
  background: #21A5AE;
}

.kontakti {
  background: #2255B3;
}

.info h3, .parmani h3, .kontakti h3 {
  font-size: 2.5rem;
  margin-bottom: 20px;
}

.info p, .parmani p {
  font-size: 1.2rem;
  font-weight: 400;
}

/* Responsive */
@media (max-width: 1024px) {
  .hero-text h1 {
    font-size: 2.8rem;
  }
  
  .hero-text h2 {
    font-size: 2rem;
  }
  
  .hero-btn {
    padding: 16px 45px;
    font-size: 1.1rem;
  }
}

@media (max-width: 768px) {
  .sakums {
    min-height: calc(100vh - 72px);
    margin-top: 72px;
    background-position: center top;
  }
  
  .hero-text h1 {
    font-size: 2.2rem;
    margin-bottom: 1rem;
  }
  
  .hero-text h2 {
    font-size: 1.6rem;
    margin-bottom: 2rem;
  }
  
  .hero-btn {
    padding: 14px 40px;
    font-size: 1rem;
  }

  .hero-text br {
    display: none;
  }

  .info,
  .parmani,
  .kontakti {
    min-height: auto;
    padding: 72px 18px;
    text-align: center;
  }

  .info h3, .parmani h3, .kontakti h3 {
    font-size: 2rem;
  }
}

@media (max-width: 480px) {
  .sakums {
    min-height: calc(100vh - 68px);
    margin-top: 68px;
  }

  .hero-text h1 {
    font-size: 1.45rem;
    line-height: 1.2;
  }
  
  .hero-text h2 {
    font-size: 1.05rem;
    line-height: 1.25;
  }
  
  .hero-btn {
    padding: 12px 18px;
    font-size: 0.9rem;
    width: 100%;
    max-width: 300px;
  }
}
</style>
