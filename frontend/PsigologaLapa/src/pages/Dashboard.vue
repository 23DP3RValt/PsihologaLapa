<script setup>
const authData = JSON.parse(localStorage.getItem('authUser') || '{}')
const user = authData.user || {}
</script>

<template>
  <section class="dashboard">
    <div class="card">
      <p class="eyebrow">Client dashboard</p>
      <h1>Sveicināts, {{ user.name || 'klients' }}</h1>
      <p>
        Tu esi ielogojies kā <strong>{{ user.email || '...' }}</strong>.
      </p>
      <p>
        Te var redzēt sarakstu ar psihologa izveidotajām konsultācijām un pētījumiem.
        Klikšķini uz <strong>Rezervēšana</strong>, lai izvēlētos brīvu laiku un pievienotu piebildes.
      </p>
    </div>
  </section>
</template>

<style scoped>
.dashboard {
  min-height: calc(100vh - 80px);
  display: grid;
  place-items: center;
  padding: 120px 24px 48px;
  background: linear-gradient(180deg, #fffdf6 0%, #eef7ff 100%);
}

.card {
  width: min(100%, 720px);
  padding: 32px;
  border-radius: 24px;
  background: white;
  box-shadow: 0 16px 42px rgba(0, 51, 102, 0.08);
}

.eyebrow {
  margin: 0 0 10px;
  text-transform: uppercase;
  letter-spacing: 0.12em;
  color: #006699;
  font-size: 0.8rem;
}

h1 {
  margin: 0 0 12px;
}

p {
  margin: 0 0 12px;
  line-height: 1.7;
}
</style>
