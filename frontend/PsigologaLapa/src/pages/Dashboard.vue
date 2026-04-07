<template>
  <section class="dashboard">
    <div class="card">
      <p class="eyebrow">Client dashboard</p>
      <h1>Laipni ludzam klienta paneli</h1>
      <p>
        Seit nonak regularie klienti pec veiksmigas pieslegsanas. Te var turpinat
        darbu ar rezervacijam un citu klienta informaciju.
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
  margin: 0;
  line-height: 1.6;
}
</style>
