<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '@/services/api'

const clients = ref([])
const selectedClientId = ref(null)
const commentText = ref('')
const nameFilter = ref('')
const emailFilter = ref('')
const participationFilter = ref('')
const loading = ref(false)
const error = ref(null)

const normalizeSearchValue = (value) => {
  return String(value ?? '')
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .toLowerCase()
}

const filteredClients = computed(() => {
  const nameTerm = normalizeSearchValue(nameFilter.value)
  const emailTerm = normalizeSearchValue(emailFilter.value)
  const participationTerm = normalizeSearchValue(participationFilter.value)

  if (!nameTerm && !emailTerm && !participationTerm) return clients.value

  return clients.value.filter((client) => {
    const clientName = normalizeSearchValue(client.name)
    const clientEmail = normalizeSearchValue(client.email)
    const participationCount = normalizeSearchValue(client.participation_count)

    return (
      clientName.includes(nameTerm) &&
      clientEmail.includes(emailTerm) &&
      participationCount.includes(participationTerm)
    )
  })
})

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
      <div class="clients-table-frame">
        <table>
          <thead>
            <tr class="filter-row">
              <th></th>
              <th>
                <input
                  v-model="nameFilter"
                  class="column-filter"
                  type="search"
                  aria-label="Meklēt pēc vārda"
                  placeholder="Meklēt vārdu"
                />
              </th>
              <th>
                <input
                  v-model="emailFilter"
                  class="column-filter"
                  type="search"
                  aria-label="Meklēt pēc e-pasta"
                  placeholder="Meklēt e-pastu"
                />
              </th>
              <th>
                <input
                  v-model="participationFilter"
                  class="column-filter"
                  type="search"
                  aria-label="Meklēt pēc piedalīšanās reižu skaita"
                  placeholder="Skaits"
                />
              </th>
              <th></th>
              <th></th>
              <th></th>
            </tr>
            <tr class="heading-row">
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
            <tr v-for="client in filteredClients" :key="client.id">
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
            <tr v-else-if="filteredClients.length === 0 && !loading">
              <td colspan="7">Nav klientu, kas atbilst meklēšanai.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <template v-if="selectedClientId !== null">
      <div
        class="card details-card"
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
    </template>
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
  box-shadow: 0 18px 45px rgba(34, 85, 179, 0.12);
}

.eyebrow {
  margin: 0 0 10px;
  text-transform: uppercase;
  letter-spacing: 0.12em;
  color: #87CC27;
  font-size: 0.8rem;
}

.lead {
  margin: 0 0 18px;
}

.error-message {
  color: #DD4125;
  font-weight: 600;
}

.clients-table-frame {
  max-height: 560px;
  overflow: auto;
  margin-top: 20px;
  border: 1px solid #e5eaf0;
  border-radius: 12px;
}

table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  min-width: 920px;
}

thead th {
  position: sticky;
  top: 0;
  z-index: 2;
  background: white;
  text-align: left;
  padding: 12px;
  border-bottom: 2px solid rgba(34, 85, 179, 0.16);
  color: var(--color-primary-start);
}

.filter-row th {
  top: 0;
  z-index: 4;
  border-bottom: 0;
  padding-bottom: 4px;
}

.heading-row th {
  top: 54px;
  z-index: 3;
}

.column-filter {
  width: 100%;
  min-width: 110px;
  border: 1px solid #d3dde8;
  border-radius: 12px;
  padding: 9px 10px;
  font: inherit;
}

.column-filter:focus {
  border-color: var(--color-primary-start);
  outline: 3px solid rgba(34, 85, 179, 0.12);
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
  background: linear-gradient(135deg, #DD4125 0%, #B22F1E 100%);
  cursor: pointer;
  padding: 10px 16px;
}

.details-button {
  background: transparent;
  color: var(--color-primary-start);
  border: 1px solid var(--color-primary-start);
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
