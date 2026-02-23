<script setup>
import { onMounted } from 'vue'
import api from '@/services/api'
import Calendar from '@/components/Calendar.vue'
import EventsList from '@/components/EventsList.vue'

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
  <body>
    <!-- <script type="module" src="/src/main.css"></script> -->
    <nav class="nav">
      <div class="logo">
        <h2><b>Sporta Psiholoģija</b></h2>
      </div>
      <div class="navButton">
        <a href="#sakums"><button>Sākums</button></a>
        <a href="#rezervet"><button>Rezervēšana</button></a>
        <a href="#info"><button>Lasāmresursi</button></a>
        <a href="#parmani"><button>Par mani</button></a>
        <a href="#kontakti"><button>Kontakti</button></a>
        <a href="#registrs"><button class="register-btn">Reģistrēties</button></a>
      </div>
    </nav>
    
    <div id="sakums" class="sakums">
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
</template>

