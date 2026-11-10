function formatTime(date) {
  return new Intl.DateTimeFormat('lv-LV', {
    hour: '2-digit',
    minute: '2-digit',
    hour12: false
  }).format(date)
}

export function renderCalendarEvent({ event }) {
  const card = document.createElement('div')
  card.className = 'calendar-event-card'

  const title = document.createElement('strong')
  title.className = 'calendar-event-title'
  title.textContent = event.title
  card.append(title)

  const time = document.createElement('span')
  time.className = 'calendar-event-time'
  if (event.allDay || !event.start) {
    time.textContent = 'Laiks nav norādīts'
  } else {
    const start = formatTime(event.start)
    const end = event.end && !event.allDay ? formatTime(event.end) : start
    time.textContent = `${start}–${end}`
  }
  card.append(time)

  return { domNodes: [card] }
}

export function calendarEventClassNames({ event }) {
  return event.extendedProps.is_booked ? ['booked-event'] : ['available-event']
}
