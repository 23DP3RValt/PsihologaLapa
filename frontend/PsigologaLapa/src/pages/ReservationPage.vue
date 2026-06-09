<script setup>
import { ref, onMounted } from 'vue'
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import interactionPlugin from '@fullcalendar/interaction'
import api from '@/services/api'

const authData = JSON.parse(localStorage.getItem('authUser') || '{}')
const isClient = authData.role === 'client'
const isSmallScreen = () => window.innerWidth <= 640

const calendarOptions = ref({
  plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
  initialView: isSmallScreen() ? 'timeGridDay' : 'dayGridMonth',
  height: 'auto',
  dayMaxEvents: true,
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
  editable: false,
  selectable: false,
  events: [],
  headerToolbar: {
    left: 'prev,next today',
    center: 'title',
    right: 'dayGridMonth,timeGridWeek,timeGridDay'
  },
  windowResize: ({ view }) => {
    view.calendar.changeView(isSmallScreen() ? 'timeGridDay' : 'dayGridMonth')
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
      color: e.is_booked ? '#DD4125' : e.color || '#21A5AE',
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
  background: linear-gradient(180deg, #2255B3 0%, #1EC4F2 100%);
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
  color: var(--color-primary-start);
  margin-bottom: 12px;
  font-size: 2rem;
}

.subtitle {
  color: #4f5c72;
  margin-bottom: 20px;
}

.calendar-wrapper :deep(.fc) {
  min-width: 0;
}

.calendar-wrapper :deep(.fc-toolbar) {
  gap: 10px;
  flex-wrap: wrap;
}

.calendar-wrapper :deep(.fc-toolbar-title) {
  font-size: clamp(1.2rem, 4vw, 1.75rem);
}

@media (max-width: 640px) {
  .reservation-page {
    padding: 88px 12px 28px;
  }

  .calendar-wrapper {
    padding: 16px;
  }

  .calendar-wrapper h2 {
    font-size: 1.45rem;
  }

  .subtitle {
    font-size: 0.95rem;
  }

  .calendar-wrapper :deep(.fc-header-toolbar) {
    align-items: stretch;
    flex-direction: column;
  }

  .calendar-wrapper :deep(.fc-toolbar-chunk) {
    display: flex;
    justify-content: center;
  }

  .calendar-wrapper :deep(.fc-button) {
    padding: 0.35rem 0.5rem;
    font-size: 0.82rem;
  }
}
</style>
