<template>
  <FullCalendar :options="calendarOptions" />
</template>

<script>
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import interactionPlugin from '@fullcalendar/interaction'
import axios from 'axios'

export default {
  components: { FullCalendar },

  data() {
    return {
      calendarOptions: {
        plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
        initialView: 'dayGridMonth',
        editable: false,
        selectable: true,
        events: this.fetchEvents,
        eventClick: this.handleEventClick
      }
    }
  },

  methods: {
    fetchEvents(fetchInfo, successCallback, failureCallback) {
      const apiUrl = 'http://localhost:8000/api/events'
      axios.get(apiUrl)
        .then(response => {
          console.log('Events fetched:', response.data)
          successCallback(response.data)
        })
        .catch(error => {
          console.error('Failed to fetch events:', error)
          console.error('API URL:', apiUrl)
          failureCallback(error)
        })
    },

    handleEventClick(info) {
      alert(
        info.event.title + "\n\n" +
        (info.event.extendedProps.description || '')
      )
    }
  }
}
</script>

