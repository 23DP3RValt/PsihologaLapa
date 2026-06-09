<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '../services/api'

const email = ref('')
const password = ref('')
const error = ref('')
const isSubmitting = ref(false)
const showPassword = ref(false)
const router = useRouter()

const login = async () => {
  error.value = ''
  isSubmitting.value = true

  try {
    const data = await api.post('/login', {
      email: email.value,
      password: password.value
    })

    const authPayload = {
      ...data,
      token: data.token
    }

    localStorage.setItem('authUser', JSON.stringify(authPayload))
    window.dispatchEvent(new Event('authUpdated'))

    if (data.role === 'psychologist') {
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
      <h2>Pieslēgties</h2>
      <p>Ievadi savus datus, un sistēma atvērs tev atbilstošo paneli.</p>

      <input v-model="email" type="email" placeholder="Epasts" />
      <div class="password-field">
        <input
          v-model="password"
          :type="showPassword ? 'text' : 'password'"
          placeholder="Parole"
        />
        <button
          type="button"
          class="password-toggle"
          :aria-label="showPassword ? 'Slept paroli' : 'Radit paroli'"
          @click="showPassword = !showPassword"
        >
          {{ showPassword ? 'Slept' : 'Rādit' }}
        </button>
      </div>

      <button type="submit" :disabled="isSubmitting">
        {{ isSubmitting ? 'Notiek ielade...' : 'Pieslēgties' }}
      </button>

      <div class="register-prompt">
        <span>Vēl nav klienta profila?</span>
        <router-link class="register-link" to="/registresana">Registrēties</router-link>
      </div>

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
}

.login-card {
  width: min(100%, 420px);
  display: grid;
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
  width: 100%;
  padding: 12px 14px;
  border: 1px solid #c6d4dd;
  border-radius: 12px;
  font-size: 1rem;
}

.password-field {
  display: grid;
  grid-template-columns: 1fr auto;
  align-items: center;
  gap: 8px;
}

.password-field input {
  min-width: 0;
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

.password-toggle {
  padding: 10px 12px;
  font-size: 0.9rem;
  background: #2255B3;
}

.register-prompt {
  display: grid;
  gap: 8px;
  justify-items: center;
  padding-top: 4px;
  color: #4f5c72;
  font-size: 0.95rem;
  text-align: center;
}

.register-link {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-height: 44px;
  width: 100%;
  border: 1px solid rgba(34, 85, 179, 0.18);
  border-radius: 999px;
  background: #eef5ff;
  color: var(--color-primary-start);
  font-weight: 700;
  text-decoration: none;
  transition: background 0.2s ease, color 0.2s ease;
}

.register-link:hover {
  background: rgba(34, 85, 179, 0.12);
  color: #17264d;
}

.error {
  color: #DD4125;
  font-weight: 600;
}

@media (max-width: 480px) {
  .login-page {
    padding: 88px 12px 32px;
  }

  .login-card {
    padding: 22px;
    border-radius: 16px;
  }

  .password-field {
    grid-template-columns: 1fr;
  }

  .password-toggle {
    width: 100%;
  }
}
</style>
