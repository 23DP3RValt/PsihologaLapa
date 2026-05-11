<script setup>
import { ref } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'

const email = ref('')
const password = ref('')
const error = ref('')
const isSubmitting = ref(false)
const router = useRouter()

const login = async () => {
  error.value = ''
  isSubmitting.value = true

  try {
    const res = await axios.post('http://127.0.0.1:8000/api/login', {
      email: email.value,
      password: password.value
    })

    const authPayload = {
      ...res.data,
      token: res.data.token
    }

    localStorage.setItem('authUser', JSON.stringify(authPayload))
    window.dispatchEvent(new Event('authUpdated'))

    if (res.data.role === 'psychologist') {
      router.push('/psihologs')
      return
    }

    router.push('/dashboard')
  } catch {
    error.value = 'Nepareizs e-pasts vai parole.'
  } finally {
    isSubmitting.value = false
  }
}
</script>

<template>
  <div class="login-page">
    <form class="login-card" @submit.prevent="login">
      <h2>Pieslegties</h2>
      <p>Ievadi savus datus, un sistema atvers tev atbilstoso paneli.</p>

      <input v-model="email" type="email" placeholder="Email" />
      <input v-model="password" type="password" placeholder="Parole" />

      <button type="submit" :disabled="isSubmitting">
        {{ isSubmitting ? 'Notiek ielade...' : 'Pieslegties' }}
      </button>

      <p v-if="error" class="error">{{ error }}</p>
    </form>
  </div>
</template>

<style scoped>
.login-page {
  min-height: calc(100vh - 80px);
  display: grid;
  place-items: center;
  padding: 120px 24px 40px;
  background: linear-gradient(180deg, #2255B3 0%, #1EC4F2 100%);
  flex-direction: column;
  gap: 14px;
  padding: 32px;
  border-radius: 24px;
  background: rgba(255, 255, 255, 0.96);
  box-shadow: 0 18px 45px rgba(34, 85, 179, 0.12);
}

.login-card h2,
.login-card p {
  margin: 0;
}

input {
  padding: 12px 14px;
  border: 1px solid #c6d4dd;
  border-radius: 12px;
  font-size: 1rem;
}

button {
  border: none;
  border-radius: 999px;
  padding: 12px 18px;
  color: white;
  background: linear-gradient(135deg, #DD4125 0%, #B22F1E 100%);
  cursor: pointer;
}

button:disabled {
  opacity: 0.7;
  cursor: wait;
}

.error {
  color: #DD4125;
  font-weight: 600;
}
</style>
