<script setup>
import { ref, onMounted } from 'vue'
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import interactionPlugin from '@fullcalendar/interaction'
import api from '@/services/api'

const calendarOptions = ref({
  plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
  initialView: 'dayGridMonth',
  editable: true,
  selectable: true,
  events: [],
  headerToolbar: {
    left: 'prev,next today',
    center: 'title',
    right: 'dayGridMonth,timeGridWeek,timeGridDay'
  },
  dateClick: async (info) => {
    const title = window.prompt('Event title:')
    if (!title) return
    const payload = { title, start: info.dateStr, end: info.dateStr }
    console.log('Creating event with payload:', payload)
    try {
      const response = await api.post('/events', payload)
      console.log('Event created:', response)
      await loadEvents()
    } catch (e) {
      console.error('Failed to create event:', e.response?.data || e.message)
      alert('Failed to create event: ' + (e.response?.data?.error || e.message))
    }
  },
  eventClick: async (info) => {
    const id = info.event.id
    if (window.confirm('Delete this event?')) {
      try {
        await api.delete(`/events/${id}`)
        await loadEvents()
      } catch (e) {
        console.error(e)
        alert('Failed to delete event')
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

async function loadEvents() {
  try {
    const events = await api.get('/events')
    console.log('Events loaded:', events)
    // FullCalendar expects id, title, start, end, color (optional)
    calendarOptions.value.events = events.map(e => ({
      id: String(e.id),
      title: e.title,
      start: e.start,
      end: e.end || e.start,
      color: e.color || '#3498db',
      extendedProps: { description: e.description }
    }))
  } catch (e) {
    console.error('Failed to load events:', e.response?.data || e.message)
  }
}

async function updateEventTime(event) {
  const id = event.id
  const start = event.start ? event.start.toISOString() : null
  const end = event.end ? event.end.toISOString() : start
  console.log('Updating event:', { id, start, end })
  try {
    await api.put(`/events/${id}`, { start, end })
    await loadEvents()
  } catch (e) {
    console.error('Failed to update event:', e.response?.data || e.message)
    alert('Failed to update event: ' + (e.response?.data?.error || e.message))
  }
}

onMounted(() => {
  loadEvents()
})
</script>

<template>
  <div class="reservation-page">
    <div class="calendar-wrapper">
      <h2>Rezervēšana</h2>
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
  margin-bottom: 24px;
  font-size: 2rem;
}
</style>
