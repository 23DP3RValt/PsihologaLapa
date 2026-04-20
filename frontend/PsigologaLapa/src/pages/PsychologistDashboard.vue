<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import api from '@/services/api'

const authData = JSON.parse(localStorage.getItem('authUser') || '{}')
const psychologist = authData.user || {}
const events = ref([])

const form = reactive({
  title: '',
  event_type: 'konsultacija',
  start: '',
  end: '',
  description: '',
  color: '#3498db'
})

const totalEvents = computed(() => events.value.length)
const bookedEvents = computed(() => events.value.filter((event) => event.is_booked).length)
const openEvents = computed(() => totalEvents.value - bookedEvents.value)

async function loadEvents() {
  try {
    events.value = await api.get('/events')
  } catch (e) {
    console.error('Could not load events:', e.response?.data || e.message)
  }
}

async function createEvent() {
  if (!form.title || !form.start) {
    alert('Lūdzu ievadi nosaukumu un sākuma laiku.')
    return
  }

  try {
    await api.post('/events', {
      title: form.title,
      event_type: form.event_type,
      start: form.start,
      end: form.end || form.start,
      description: form.description,
      color: form.color
    })

    alert('Pasākums pievienots veiksmīgi.')
    Object.assign(form, {
      title: '',
      event_type: 'konsultacija',
      start: '',
      end: '',
      description: '',
      color: '#3498db'
    })
    await loadEvents()
  } catch (e) {
    console.error('Failed to create event:', e.response?.data || e.message)
    alert('Neizdevās pievienot pasākumu: ' + (e.response?.data?.error || e.message))
  }
}

async function removeEvent(id) {
  if (!window.confirm('Vai tiešām vēlies dzēst šo notikumu?')) {
    return
  }

  try {
    await api.delete(`/events/${id}`)
    await loadEvents()
  } catch (e) {
    console.error('Delete failed:', e.response?.data || e.message)
    alert('Neizdevās dzēst notikumu.')
  }
}

onMounted(() => {
  loadEvents()
})
</script>

<template>
  <section class="dashboard">
    <div class="card dashboard-card">
      <p class="eyebrow">Psychologist dashboard</p>
      <h1>Sveicināts, {{ psychologist.name || 'psiholog' }}</h1>
      <p class="lead">
        Šeit vari pievienot jaunas konsultācijas vai pētījumus, un klienti var pieteikties tiem.
      </p>

      <div class="details">
        <p><strong>E-pasts:</strong> {{ psychologist.email || '-' }}</p>
        <p><strong>Specializācija:</strong> {{ psychologist.specialization || '-' }}</p>
        <p><strong>Bio:</strong> {{ psychologist.bio || 'Nav pievienots apraksts.' }}</p>
        <p><strong>Izveidotie notikumi:</strong> {{ totalEvents }}</p>
        <p><strong>Brīvie laiki:</strong> {{ openEvents }}</p>
        <p><strong>Aizņemtie laiki:</strong> {{ bookedEvents }}</p>
      </div>
    </div>

    <div class="card form-card">
      <h2>Pievienot jaunu notikumu</h2>
      <div class="form-row">
        <label>Nosaukums</label>
        <input v-model="form.title" placeholder="Piem., Konsultācija ar klientu" />
      </div>
      <div class="form-row">
        <label>Veids</label>
        <select v-model="form.event_type">
          <option value="konsultacija">Konsultācija</option>
          <option value="petijums">Pētījums</option>
        </select>
      </div>
      <div class="form-row">
        <label>Sākuma datums un laiks</label>
        <input v-model="form.start" type="datetime-local" />
      </div>
      <div class="form-row">
        <label>Beigu datums un laiks</label>
        <input v-model="form.end" type="datetime-local" />
      </div>
      <div class="form-row">
        <label>Apraksts</label>
        <textarea v-model="form.description" placeholder="Papildus informācija"></textarea>
      </div>
      <div class="form-row">
        <label>Krāsa</label>
        <input v-model="form.color" type="color" />
      </div>
      <button @click.prevent="createEvent">Saglabāt notikumu</button>
    </div>

    <div class="card table-card">
      <h2>Notikumu saraksts</h2>
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>Nosaukums</th>
            <th>Veids</th>
            <th>Datums</th>
            <th>Status</th>
            <th>Klients</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="event in events" :key="event.id">
            <td>{{ event.id }}</td>
            <td>{{ event.title }}</td>
            <td>{{ event.event_type === 'petijums' ? 'Pētījums' : 'Konsultācija' }}</td>
            <td>{{ event.start }} - {{ event.end || event.start }}</td>
            <td>{{ event.is_booked ? 'Aizņemts' : 'Brīvs' }}</td>
            <td>{{ event.client_name || '-' }}</td>
            <td><button class="delete-button" @click="removeEvent(event.id)">Dzēst</button></td>
          </tr>
          <tr v-if="events.length === 0">
            <td colspan="7">Nav pievienotu pasākumu.</td>
          </tr>
        </tbody>
      </table>
    </div>
  </section>
</template>

<style scoped>
.dashboard {
  min-height: calc(100vh - 80px);
  display: grid;
  gap: 24px;
  padding: 100px 24px 48px;
  background: linear-gradient(180deg, #f7fbff 0%, #eef7ff 100%);
}

.dashboard-card,
.form-card,
.table-card {
  width: min(100%, 1100px);
  padding: 28px;
  border-radius: 24px;
  background: white;
  box-shadow: 0 18px 45px rgba(0, 51, 102, 0.08);
}

.eyebrow {
  margin: 0 0 10px;
  text-transform: uppercase;
  letter-spacing: 0.12em;
  color: #d46b08;
  font-size: 0.8rem;
}

.lead {
  margin: 0 0 18px;
}

.details {
  display: grid;
  gap: 10px;
  margin-top: 20px;
}

.form-row {
  display: grid;
  gap: 8px;
  margin-bottom: 16px;
}

.form-row label {
  font-weight: 600;
}

input,
select,
textarea {
  width: 100%;
  padding: 12px 14px;
  border: 1px solid #d3dde8;
  border-radius: 12px;
  font-size: 1rem;
}

textarea {
  min-height: 100px;
  resize: vertical;
}

button {
  border: none;
  border-radius: 999px;
  padding: 12px 20px;
  color: white;
  background: linear-gradient(135deg, #006699 0%, #003366 100%);
  cursor: pointer;
}

table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
}

thead th {
  text-align: left;
  padding: 12px;
  border-bottom: 2px solid #e5eaf0;
  color: #003366;
}

tbody td {
  padding: 14px 12px;
  border-bottom: 1px solid #eef2f7;
}

.delete-button {
  border: none;
  color: #b42318;
  background: transparent;
  cursor: pointer;
  font-weight: 700;
}
</style>
