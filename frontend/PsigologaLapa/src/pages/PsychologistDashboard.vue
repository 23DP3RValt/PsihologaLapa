<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import interactionPlugin from '@fullcalendar/interaction'
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

const calendarOptions = ref({
  plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
  initialView: 'dayGridMonth',
  slotLabelFormat: {
    hour: '2-digit',
    minute: '2-digit',
    hour12: false
  },
  eventTimeFormat: {
    hour: '2-digit',
    minute: '2-digit',
    hour12: false
  },
  editable: true,
  selectable: true,
  events: [],
  headerToolbar: {
    left: 'prev,next today',
    center: 'title',
    right: 'dayGridMonth,timeGridWeek,timeGridDay'
  },
  dateClick: async (info) => {
    const title = window.prompt('Ievadi notikuma nosaukumu:', 'Konsultācija')
    if (!title) return

    const eventTypeInput = window.prompt('Izvēlies veidu: "konsultacija" vai "petijums"', 'konsultacija')
    if (!eventTypeInput) return

    const eventType = eventTypeInput.trim().toLowerCase()
    if (!['konsultacija', 'petijums'].includes(eventType)) {
      alert('Lūdzu izvēlies "konsultacija" vai "petijums".')
      return
    }

    const payload = {
      title,
      event_type: eventType,
      start: info.dateStr,
      end: info.dateStr,
      description: ''
    }

    try {
      await api.post('/events', payload)
      await loadEvents()
      alert('Konsultācija pievienota kalendārā.')
    } catch (e) {
      console.error('Failed to create event from calendar:', e.response?.data || e.message)
      alert('Neizdevās pievienot konsultāciju. Pārbaudi konsoli.')
    }
  },
  eventClick: async (info) => {
    const event = info.event
    if (window.confirm('Dzēst šo notikumu?')) {
      try {
        await api.delete(`/events/${event.id}`)
        await loadEvents()
      } catch (e) {
        console.error('Delete failed:', e.response?.data || e.message)
        alert('Neizdevās dzēst notikumu.')
      }
    }
  },
  eventDrop: async (info) => {
    await updateEventTime(info.event)
  },
  eventResize: async (info) => {
    await updateEventTime(info.event)
  }
})

const totalEvents = computed(() => events.value.length)
const bookedEvents = computed(() => events.value.filter((event) => event.is_booked).length)
const openEvents = computed(() => totalEvents.value - bookedEvents.value)

async function loadEvents() {
  try {
    events.value = await api.get('/events')
    calendarOptions.value.events = events.value.map((event) => ({
      id: String(event.id),
      title: `${event.title} (${event.event_type === 'petijums' ? 'Pētījums' : 'Konsultācija'})`,
      start: event.start,
      end: event.end || event.start,
      color: event.is_booked ? '#d9534f' : event.color || '#3498db',
      extendedProps: {
        event_type: event.event_type,
        is_booked: event.is_booked,
        client_name: event.client_name || null,
        description: event.description || ''
      }
    }))
  } catch (e) {
    console.error('Could not load events:', e.response?.data || e.message)
  }
}

async function updateEventTime(event) {
  const id = event.id
  const start = event.startStr || (event.start ? event.start.toISOString().slice(0, 19) : null)
  const end = event.endStr || (event.end ? event.end.toISOString().slice(0, 19) : start)
  try {
    await api.put(`/events/${id}`, { start, end })
    await loadEvents()
  } catch (e) {
    console.error('Failed to update event time:', e.response?.data || e.message)
    alert('Neizdevās atjaunināt notikuma laiku.')
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
      <div class="details">
        <p><strong>Klientu pārvaldība:</strong>
          <router-link class="secondary-link" to="/psihologs/clients">Atvērt klientu lapu</router-link>
        </p>
      </div>
    </div>

    <div class="card calendar-card">
      <h2>Kalendārs</h2>
      <p>Klikšķini uz datuma, lai pievienotu jaunu konsultāciju vai pētījumu tieši kalendārā.</p>
      <FullCalendar :options="calendarOptions" />
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
        <label>Sākuma datums un laiks (24h formatā)</label>
        <input v-model="form.start" type="datetime-local" lang="en-GB" />
      </div>
      <div class="form-row">
        <label>Beigu datums un laiks (24h formatā)</label>
        <input v-model="form.end" type="datetime-local" lang="en-GB" />
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
.table-card,
.calendar-card {
  width: min(100%, 1100px);
  padding: 28px;
  border-radius: 24px;
  background: white;
  box-shadow: 0 18px 45px rgba(0, 51, 102, 0.08);
}

.calendar-card {
  display: grid;
  gap: 16px;
}

.calendar-card .fc {
  border-radius: 16px;
  overflow: hidden;
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
