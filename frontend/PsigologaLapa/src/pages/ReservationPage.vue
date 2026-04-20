<script setup>
import { ref, onMounted } from 'vue'
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import interactionPlugin from '@fullcalendar/interaction'
import api from '@/services/api'

const authData = JSON.parse(localStorage.getItem('authUser') || '{}')
const isClient = authData.role === 'client'

const calendarOptions = ref({
  plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
  initialView: 'dayGridMonth',
  editable: false,
  selectable: false,
  events: [],
  headerToolbar: {
    left: 'prev,next today',
    center: 'title',
    right: 'dayGridMonth,timeGridWeek,timeGridDay'
  },
  eventClick: async (info) => {
    const event = info.event
    const booked = event.extendedProps?.is_booked
    const eventType = event.extendedProps?.event_type || 'pasākums'

    if (booked) {
      alert(`Šis ${eventType} jau ir aizņemts. Pieteicies: ${event.extendedProps?.client_name || '----'}`)
      return
    }

    if (!isClient) {
      alert('Tikai klienti var pieteikties konsultācijām un pētījumiem.')
      return
    }

    const clientNote = window.prompt('Pievieno piebildi pieteikumam (pēc izvēles):')
    if (clientNote === null) {
      return
    }

    try {
      await api.post(`/events/${event.id}/signup`, {
        client_note: clientNote.trim() || null
      })
      alert('Jūsu pieteikums ir saglabāts. Psihologs to redzēs blakus.')
      await loadEvents()
    } catch (e) {
      console.error('Signup failed:', e.response?.data || e.message)
      alert('Neizdevās pieteikties: ' + (e.response?.data?.error || e.message))
    }
  }
})

async function loadEvents() {
  try {
    const events = await api.get('/events')
    calendarOptions.value.events = events.map((e) => ({
      id: String(e.id),
      title: `${e.title} (${e.event_type === 'petijums' ? 'Pētījums' : 'Konsultācija'})`,
      start: e.start,
      end: e.end || e.start,
      color: e.is_booked ? '#d9534f' : e.color || '#3498db',
      extendedProps: {
        description: e.description,
        event_type: e.event_type,
        is_booked: e.is_booked,
        client_name: e.client_name
      }
    }))
  } catch (e) {
    console.error('Failed to load events:', e.response?.data || e.message)
    alert('Nevar ielādēt pieejamos laikus. Pārbaudi serveri.')
  }
}

onMounted(() => {
  loadEvents()
})
</script>

<template>
  <div class="reservation-page">
    <div class="calendar-wrapper">
      <h2>Rezervē konsultāciju vai pētījumu</h2>
      <p class="subtitle">Klikšķini uz atvērtā laika, lai pieteiktos. Ja laiks ir aizņemts, tas parādīsies sarkans.</p>
      <FullCalendar :options="calendarOptions" />
    </div>
  </div>
</template>

<style scoped>
.reservation-page {
  min-height: 100vh;
  background-color: #1e3a5f;
  padding: 100px 20px 40px;
  margin-top: 0;
}

.calendar-wrapper {
  width: 100%;
  max-width: 1100px;
  margin: 0 auto;
  background: white;
  padding: 24px;
  border-radius: 8px;
  box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
}

.calendar-wrapper h2 {
  color: #003366;
  margin-bottom: 12px;
  font-size: 2rem;
}

.subtitle {
  color: #4f5c72;
  margin-bottom: 20px;
}
</style>
