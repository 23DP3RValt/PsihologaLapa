<script setup>
import { ref, onMounted } from 'vue'
import api from '@/services/api'

const clients = ref([])
const selectedClientId = ref(null)
const commentText = ref('')
const loading = ref(false)
const error = ref(null)

const loadClients = async () => {
  loading.value = true
  error.value = null

  try {
    const data = await api.get('/psychologist/clients')
    clients.value = data.clients || []
  } catch (e) {
    error.value = e.response?.data?.message || e.message || 'Neizdevās ielādēt klientu sarakstu.'
  } finally {
    loading.value = false
  }
}

const selectClient = (clientId) => {
  selectedClientId.value = selectedClientId.value === clientId ? null : clientId
  commentText.value = ''
}

const getClientDetails = (clientId) => {
  return clients.value.find((client) => client.id === clientId)
}

const submitComment = async (clientId) => {
  if (!commentText.value.trim()) {
    alert('Lūdzu ieraksti komentāru.')
    return
  }

  try {
    const response = await api.post(`/psychologist/clients/${clientId}/comments`, {
      comment: commentText.value.trim(),
    })

    const client = getClientDetails(clientId)
    if (client) {
      client.psychologist_comments.unshift(response)
      client.last_comment = response.comment
    }
    commentText.value = ''
    alert('Komentārs saglabāts.')
  } catch (e) {
    console.error('Save comment failed:', e.response?.data || e.message)
    alert('Neizdevās saglabāt komentāru. Pārbaudi tīklu vai servera žurnālu.')
  }
}

const formatDateTime = (value) => {
  if (!value) return '-'
  return new Date(value).toLocaleString('lv-LV', {
    dateStyle: 'medium',
    timeStyle: 'short',
  })
}

onMounted(() => {
  loadClients()
})
</script>

<template>
  <section class="dashboard">
    <div class="card dashboard-card">
      <p class="eyebrow">Psihologa klientu pārvaldība</p>
      <h1>Klientu saraksts</h1>
      <p class="lead">
        Šeit ir redzami reģistrētie klienti, cik reizes tie ir piedalījušies pie psihologa pierakstiem,
        klienta piezīmes par pieteikšanos un psihologa komentāru vēsture.
      </p>
      <p>
        <strong>Kopā klientu:</strong> {{ clients.length }}
      </p>
      <p v-if="error" class="error-message">{{ error }}</p>
      <p v-if="loading">Datu ielāde...</p>
    </div>

    <div class="card table-card">
      <h2>Klientu informācija</h2>
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>Vārds</th>
            <th>E-pasts</th>
            <th>Piedalījies</th>
            <th>Jaunākā piezīme</th>
            <th>Psihologa komentārs</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="client in clients" :key="client.id">
            <td>{{ client.id }}</td>
            <td>{{ client.name }}</td>
            <td>{{ client.email }}</td>
            <td>{{ client.participation_count }}</td>
            <td>{{ client.signed_up_events?.[0]?.client_note || '-' }}</td>
            <td>{{ client.last_comment || '-' }}</td>
            <td>
              <button class="details-button" @click="selectClient(client.id)">
                {{ selectedClientId === client.id ? 'Slēpt detaļas' : 'Skatīt detaļas' }}
              </button>
            </td>
          </tr>
          <tr v-if="clients.length === 0 && !loading">
            <td colspan="7">Nav reģistrētu klientu.</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div
      class="card details-card"
      v-if="selectedClientId !== null"
      v-for="client in clients"
      :key="client.id + '-details'"
      v-show="client.id === selectedClientId"
    >
      <h2>Detālas par {{ client.name }}</h2>
      <div class="details-grid">
        <div>
          <p><strong>E-pasts:</strong> {{ client.email }}</p>
          <p><strong>Tālrunis:</strong> {{ client.talrunis || '-' }}</p>
          <p><strong>Dzimšanas datums:</strong> {{ client.birthdate || '-' }}</p>
          <p><strong>Konsultāciju skaits:</strong> {{ client.participation_count }}</p>
          <p><strong>Konsultācijas:</strong> {{ client.participation_summary.konsultacija || 0 }}</p>
          <p><strong>Pētījumi:</strong> {{ client.participation_summary.petijums || 0 }}</p>
        </div>

        <div>
          <p><strong>Pēdējais psihologa komentārs:</strong></p>
          <p>{{ client.last_comment || 'Nav komentāru.' }}</p>
        </div>
      </div>

      <div class="section-block">
        <h3>Klienta pieteikšanās piezīmes</h3>
        <div v-if="client.signed_up_events.length === 0">
          Nav pieteikšanās piezīmju.
        </div>
        <ul v-else>
          <li v-for="event in client.signed_up_events" :key="event.id">
            <strong>{{ event.title }}</strong> (<em>{{ event.event_type === 'petijums' ? 'Pētījums' : 'Konsultācija' }}</em>)<br />
            {{ formatDateTime(event.start) }} - {{ formatDateTime(event.end) }}<br />
            <span><strong>Klienta piezīme:</strong> {{ event.client_note || 'Nav piezīmes' }}</span>
          </li>
        </ul>
      </div>

      <div class="section-block">
        <h3>Psihologa komentāru vēsture</h3>
        <div v-if="client.psychologist_comments.length === 0">
          Nav saglabātu komentāru.
        </div>
        <ul v-else>
          <li v-for="comment in client.psychologist_comments" :key="comment.id">
            <strong>{{ comment.created_at }}</strong><br />
            {{ comment.comment }}
          </li>
        </ul>
      </div>

      <div class="section-block">
        <h3>Pievienot jaunu komentāru</h3>
        <textarea v-model="commentText" placeholder="Raksti komentāru par klientu..."></textarea>
        <button @click.prevent="submitComment(client.id)">Saglabāt komentāru</button>
      </div>
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
.table-card,
.details-card {
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

.error-message {
  color: #b42318;
  font-weight: 600;
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

button,
textarea {
  border-radius: 12px;
}

button {
  border: none;
  color: white;
  background: linear-gradient(135deg, #006699 0%, #003366 100%);
  cursor: pointer;
  padding: 10px 16px;
}

.details-button {
  background: transparent;
  color: #003366;
  border: 1px solid #003366;
}

section .details-card {
  display: block;
}

.details-grid {
  display: grid;
  gap: 18px;
  grid-template-columns: repeat(2, minmax(0, 1fr));
}

.section-block {
  margin-top: 20px;
}

ul {
  list-style: none;
  padding: 0;
}

ul li {
  padding: 12px;
  border: 1px solid #e5eaf0;
  border-radius: 12px;
  margin-bottom: 12px;
}

textarea {
  width: 100%;
  min-height: 120px;
  border: 1px solid #d3dde8;
  padding: 12px;
  margin-top: 10px;
}
</style>
