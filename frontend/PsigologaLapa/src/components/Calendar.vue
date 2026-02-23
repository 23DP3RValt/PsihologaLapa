<template>
  <div class="calendar-wrapper">
    <FullCalendar :options="calendarOptions" />
  </div>
</template>

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
    try {
      await api.post('/events', payload)
      await loadEvents()
    } catch (e) {
      console.error(e)
      alert('Failed to create event')
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
    // FullCalendar expects id, title, start, end, color (optional)
    calendarOptions.value.events = events.map(e => ({
      id: String(e.id),
      title: e.title,
      start: e.start,
      end: e.end || e.start,
      color: e.color,
      extendedProps: { description: e.description }
    }))
  } catch (e) {
    console.error('Failed to load events', e)
  }
}

async function updateEventTime(event) {
  const id = event.id
  const start = event.start ? event.start.toISOString() : null
  const end = event.end ? event.end.toISOString() : start
  try {
    await api.put(`/events/${id}`, { start, end })
    await loadEvents()
  } catch (e) {
    console.error(e)
    alert('Failed to update event')
  }
}

onMounted(() => {
  loadEvents()
})
</script>
<!-- 
<style scoped>
.calendar-wrapper {
  width: 100%;
  max-width: 1100px;
  margin: 120px auto 40px; /* leave space for fixed nav */
  background: white;
  padding: 16px;
  border-radius: 8px;
  box-shadow: 0 6px 18px rgba(0,0,0,0.08);
}
</style> -->
