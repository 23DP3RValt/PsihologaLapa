<template>
  <div class="calendar-wrapper">
    <FullCalendar :options="calendarOptions" />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import FullCalendar from '@fullcalendar/vue3'
import lvLocale from '@fullcalendar/core/locales/lv'
import dayGridPlugin from '@fullcalendar/daygrid'
import interactionPlugin from '@fullcalendar/interaction'
import { calendarEventClassNames, renderCalendarEvent } from '@/utils/calendarEvents'
import api from '@/services/api'



const calendarOptions = ref({
  plugins: [dayGridPlugin, interactionPlugin],
  initialView: 'dayGridWeek',
  locale: lvLocale,
  firstDay: 1,
  editable: true,
  selectable: true,
  eventContent: renderCalendarEvent,
  eventClassNames: calendarEventClassNames,
  events: [],
  headerToolbar: {
    left: 'prev,next today',
    center: 'title',
    right: ''
  },
  dateClick: async (info) => {
    const title = window.prompt('Ievadi nodarbības nosaukumu:')
    if (!title) return

    const startTime = window.prompt('Ievadi sākuma laiku (HH:MM):', '09:00')
    const endTime = window.prompt('Ievadi beigu laiku (HH:MM):', '10:00')
    const timePattern = /^([01]\d|2[0-3]):[0-5]\d$/
    if (!startTime || !timePattern.test(startTime) || !endTime || !timePattern.test(endTime)) {
      alert('Sākuma un beigu laiku ievadi 24h formātā, piemēram, 09:00 un 10:00.')
      return
    }

    const start = `${info.dateStr}T${startTime}`
    const end = `${info.dateStr}T${endTime}`
    if (new Date(end) <= new Date(start)) {
      alert('Beigu laikam jābūt vēlākam par sākuma laiku.')
      return
    }

    const payload = { title, event_type: 'konsultacija', start, end }
    try {
      await api.post('/events', payload)
      await loadEvents()
    } catch (e) {
      console.error('Failed to create event:', e.response?.data || e.message)
      alert('Neizdevās izveidot nodarbību.')
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
    calendarOptions.value.events = events.map((e) => ({
      id: String(e.id),
      title: e.title,
      start: e.start,
      end: e.end || e.start,
      color: e.is_booked ? '#198754' : e.color || '#21A5AE',
      extendedProps: {
        description: e.description,
        is_booked: e.is_booked
      }
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
<style scoped>
.calendar-wrapper :deep(.fc-daygrid-event) {
  margin: 3px 0;
  border: 0;
  background: transparent;
  white-space: normal;
}

.calendar-wrapper :deep(.calendar-event-card) {
  display: grid;
  gap: 2px;
  padding: 5px 7px;
  border: 1px solid currentColor;
  border-radius: 6px;
  color: #176f96;
  font-size: 0.85rem;
  line-height: 1.25;
  white-space: normal;
}

.calendar-wrapper :deep(.fc-event.booked-event .calendar-event-card) {
  color: #16783b;
  background: #e8f6ec;
  font-weight: 700;
}

.calendar-wrapper :deep(.calendar-event-time) {
  font-size: 0.8rem;
}
</style>
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
