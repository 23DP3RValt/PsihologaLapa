<template>
  <div class="events-list">
    <h3>Events (from /api/events)</h3>
    <div v-if="loading">Loading events...</div>
    <div v-else-if="error" class="error">Failed to load events: {{ error }}</div>
    <ul v-else>
      <li v-for="ev in events" :key="ev.id" class="event-item">
        <strong>{{ ev.title }}</strong>
        <div class="meta">{{ formatDate(ev.start) }} — {{ ev.end ? formatDate(ev.end) : '' }}</div>
        <div v-if="ev.description" class="desc">{{ ev.description }}</div>
      </li>
    </ul>
    <div v-if="events.length===0 && !loading">No events found.</div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/services/api'

const events = ref([])
const loading = ref(true)
const error = ref(null)

function formatDate(s) {
  try { return new Date(s).toLocaleString() } catch { return s }
}

async function load() {
  loading.value = true
  error.value = null
  try {
    const data = await api.get('/events')
    events.value = data
  } catch (e) {
    error.value = e.message || String(e)
  } finally {
    loading.value = false
  }
}

onMounted(() => { load() })
</script>

<style scoped>
.events-list { max-width: 900px; margin: 40px auto; background: #fff; padding: 16px; border-radius: 8px; }
.events-list h3 { margin-bottom: 12px }
.event-item { padding: 8px 0; border-bottom: 1px solid #eee }
.event-item:last-child { border-bottom: none }
.meta { color: #666; font-size: 0.9rem }
.desc { margin-top: 6px }
.error { color: #a00 }
</style>
