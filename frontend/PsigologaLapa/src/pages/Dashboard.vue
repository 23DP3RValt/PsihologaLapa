<script setup>
import { ref, onMounted } from 'vue'
import api from '@/services/api'

const authData = JSON.parse(localStorage.getItem('authUser') || '{}')
const user = authData.user || {}
const profile = ref(null)
const loading = ref(false)
const error = ref(null)

const formatDateTime = (value) => {
  if (!value) return '-'
  return new Date(value).toLocaleString('lv-LV', {
    dateStyle: 'medium',
    timeStyle: 'short',
  })
}

const loadProfile = async () => {
  loading.value = true
  error.value = null

  try {
    const data = await api.get('/client/profile')
    profile.value = data.profile
  } catch (e) {
    error.value = e.response?.data?.message || e.message || 'Neizdevās ielādēt profila datus.'
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadProfile()
})
</script>

<template>
  <section class="dashboard">
    <div class="card">
      <p class="eyebrow">Client dashboard</p>
      <h1>Sveicināts, {{ profile?.name || user.name || 'klients' }}</h1>
      <p>
        Tu esi ielogojies kā <strong>{{ profile?.email || user.email || '...' }}</strong>.
      </p>
      <p>
        Te var redzēt sarakstu ar psihologa izveidotajām konsultācijām un pētījumiem.
        Klikšķini uz <strong>Rezervēšana</strong>, lai izvēlētos brīvu laiku un pievienotu piebildes.
      </p>
      <p v-if="error" class="error-message">{{ error }}</p>
      <p v-if="loading">Datu ielāde...</p>
    </div>

    <div class="card" v-if="profile && profile.signed_up_events && profile.signed_up_events.length">
      <h2>Pieteiktās konsultācijas un pētījumi</h2>
      <ul class="event-list">
        <li v-for="event in profile.signed_up_events" :key="event.id">
          <strong>{{ event.title }}</strong> (<em>{{ event.event_type === 'petijums' ? 'Pētījums' : 'Konsultācija' }}</em>)<br />
          <span>{{ formatDateTime(event.start) }} - {{ formatDateTime(event.end) }}</span><br />
          <span><strong>Psihologs:</strong> {{ event.psychologist_name || 'nav norādīts' }}</span><br />
          <span><strong>Klienta piezīme:</strong> {{ event.client_note || 'Nav piezīmes' }}</span>
        </li>
      </ul>
    </div>

    <div class="card" v-if="profile && profile.signed_up_events && profile.signed_up_events.length === 0 && !loading">
      <h2>Pieteikto konsultāciju pārskats</h2>
      <p>Tev šobrīd nav rezervētu konsultāciju vai pētījumu.</p>
    </div>
  </section>
</template>

<style scoped>
.dashboard {
  min-height: calc(100vh - 80px);
  display: grid;
  place-items: center;
  padding: 120px 24px 48px;
  background: linear-gradient(180deg, #fffdf6 0%, #eef7ff 100%);
}

.card {
  width: min(100%, 720px);
  padding: 32px;
  border-radius: 24px;
  background: white;
  box-shadow: 0 16px 42px rgba(0, 51, 102, 0.08);
}

.eyebrow {
  margin: 0 0 10px;
  text-transform: uppercase;
  letter-spacing: 0.12em;
  color: #006699;
  font-size: 0.8rem;
}

h1 {
  margin: 0 0 12px;
}

p {
  margin: 0 0 12px;
  line-height: 1.7;
}

.event-list,
.comment-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.event-list li,
.comment-list li {
  padding: 18px 0;
  border-bottom: 1px solid #eef2f7;
}

.event-list li:last-child,
.comment-list li:last-child {
  border-bottom: none;
}
</style>
