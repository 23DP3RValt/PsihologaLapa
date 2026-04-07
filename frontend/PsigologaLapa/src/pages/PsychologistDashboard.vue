<script setup>
const authData = JSON.parse(localStorage.getItem('authUser') || '{}')
const psychologist = authData.user || {}
</script>

<template>
  <section class="dashboard">
    <div class="card">
      <p class="eyebrow">Psychologist dashboard</p>
      <h1>Sveicinats, {{ psychologist.name || 'psiholog' }}</h1>
      <p class="lead">
        Seit nonak lietotaji, kuri piesledzas ar psihologa registracijas datiem.
      </p>

      <div class="details">
        <p><strong>E-pasts:</strong> {{ psychologist.email || '-' }}</p>
        <p><strong>Specializacija:</strong> {{ psychologist.specialization || '-' }}</p>
        <p><strong>Bio:</strong> {{ psychologist.bio || 'Nav pievienots apraksts.' }}</p>
      </div>
    </div>
  </section>
</template>

<style scoped>
.dashboard {
  min-height: calc(100vh - 80px);
  display: grid;
  place-items: center;
  padding: 120px 24px 48px;
  background:
    radial-gradient(circle at top right, rgba(255, 153, 51, 0.22), transparent 30%),
    linear-gradient(180deg, #fff8f2 0%, #f3fbff 100%);
}

.card {
  width: min(100%, 760px);
  padding: 32px;
  border-radius: 24px;
  background: rgba(255, 255, 255, 0.96);
  box-shadow: 0 18px 45px rgba(0, 51, 102, 0.1);
}

.eyebrow {
  margin: 0 0 10px;
  text-transform: uppercase;
  letter-spacing: 0.12em;
  color: #d46b08;
  font-size: 0.8rem;
}

.lead {
  margin-bottom: 18px;
}

.details {
  display: grid;
  gap: 10px;
}
</style>
