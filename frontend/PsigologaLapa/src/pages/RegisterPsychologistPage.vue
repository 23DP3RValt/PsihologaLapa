<script setup>
import { computed, ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/services/api'

const name = ref('')
const email = ref('')
const password = ref('')
const confirmPassword = ref('')
const specialization = ref('')
const bio = ref('')
const showPassword = ref(false)

const errorMessage = ref('')
const successMessage = ref('')
const isSubmitting = ref(false)

const isValidEmail = computed(() => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value))
const hasLowercase = computed(() => /[a-z]/.test(password.value))
const hasUppercase = computed(() => /[A-Z]/.test(password.value))
const hasNumber = computed(() => /[0-9]/.test(password.value))
const hasLength = computed(() => password.value.length >= 8)

const passwordsMatch = computed(() => {
  return password.value === confirmPassword.value && password.value !== ''
})

const allFieldsFilled = computed(() => {
  return (
    name.value &&
    email.value &&
    password.value &&
    confirmPassword.value &&
    specialization.value
  )
})

const isFormValid = computed(() => {
  return (
    allFieldsFilled.value &&
    isValidEmail.value &&
    hasLowercase.value &&
    hasUppercase.value &&
    hasNumber.value &&
    hasLength.value &&
    passwordsMatch.value
  )
})

const router = useRouter()

const resetForm = () => {
  name.value = ''
  email.value = ''
  password.value = ''
  confirmPassword.value = ''
  specialization.value = ''
  bio.value = ''
}

const submitForm = async () => {
  errorMessage.value = ''
  successMessage.value = ''

  if (!isFormValid.value) {
    errorMessage.value = 'Lūdzu aizpildi visus laukus pareizi.'
    return
  }

  isSubmitting.value = true

  try {
    const response = await api.post('/register-psychologist', {
      name: name.value,
      email: email.value,
      password: password.value,
      password_confirmation: confirmPassword.value,
      specialization: specialization.value,
      bio: bio.value
    })

    const authPayload = {
      ...response,
      token: response.token
    }

    localStorage.setItem('authUser', JSON.stringify(authPayload))
    successMessage.value = 'Psihologa profils veiksmīgi izveidots.'
    resetForm()
    router.push('/psihologs')
  } catch (error) {
    if (error.response?.data?.message) {
      errorMessage.value = error.response.data.message
    } else {
      errorMessage.value = 'Savienojuma kļūda.'
    }
  } finally {
    isSubmitting.value = false
  }
}
</script>

<template>
  <form @submit.prevent="submitForm" class="registration">
    <div class="intro">
      <h1>Psihologa reģistrācija</h1>
      <p>Šī forma ir paredzēta psihologa konta izveidei. Sistēmā drīkst būt tikai viens psihologs.</p>
    </div>

    <div class="form-group">
      <label>Vārds</label>
      <input v-model="name" type="text" placeholder="Vārds" />
    </div>

    <div class="form-group">
      <label>E-pasts</label>
      <input v-model="email" type="email" placeholder="Email" />
    </div>

    <div class="form-group">
      <label>Parole</label>
      <div class="password-field">
        <input v-model="password" :type="showPassword ? 'text' : 'password'" />
        <button
          type="button"
          class="password-toggle"
          :aria-label="showPassword ? 'Slept paroli' : 'Radit paroli'"
          @click="showPassword = !showPassword"
        >
          {{ showPassword ? 'Slept' : 'Radit' }}
        </button>
      </div>
    </div>

    <div class="rules">
      <p :class="{ valid: hasLowercase, invalid: !hasLowercase }">Mazais burts</p>
      <p :class="{ valid: hasUppercase, invalid: !hasUppercase }">Lielais burts</p>
      <p :class="{ valid: hasNumber, invalid: !hasNumber }">Skaitlis</p>
      <p :class="{ valid: hasLength, invalid: !hasLength }">Vismaz 8 simboli</p>
    </div>

    <div class="form-group">
      <label>Atkārtot paroli</label>
      <div class="password-field">
        <input v-model="confirmPassword" :type="showPassword ? 'text' : 'password'" />
        <button
          type="button"
          class="password-toggle"
          :aria-label="showPassword ? 'Slept paroli' : 'Radit paroli'"
          @click="showPassword = !showPassword"
        >
          {{ showPassword ? 'Slept' : 'Radit' }}
        </button>
      </div>
    </div>

    <p v-if="confirmPassword && !passwordsMatch" class="invalid">Paroles nesakrīt.</p>

    <div class="form-group">
      <label>Specializācija</label>
      <input v-model="specialization" type="text" placeholder="Specializācija" />
    </div>

    <div class="form-group">
      <label>Bio</label>
      <textarea v-model="bio" placeholder="Īss apraksts par sevi"></textarea>
    </div>

    <p v-if="errorMessage" class="invalid">{{ errorMessage }}</p>
    <p v-if="successMessage" class="success">{{ successMessage }}</p>

    <button type="submit" :disabled="isSubmitting">
      {{ isSubmitting ? 'Iekārto...' : 'Reģistrēt psihologu' }}
    </button>
  </form>
</template>

<style scoped>
.registration {
  display: flex;
  flex-direction: column;
  justify-content: center;
  gap: 16px;
  padding: 120px 24px 48px;
  width: min(100%, 420px);
  margin: 0 auto;
}

.intro h1,
.intro p {
  margin: 0;
}

.intro {
  display: grid;
  gap: 8px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

input,
textarea {
  padding: 8px;
  border: 1px solid #000;
  border-radius: 4px;
}

.password-field {
  display: grid;
  grid-template-columns: 1fr auto;
  gap: 8px;
  align-items: center;
}

.password-field input {
  min-width: 0;
}

.password-toggle {
  min-width: 64px;
  padding: 8px;
}

.rules {
  font-size: 14px;
}

.valid {
  text-decoration: underline;
}

.invalid {
  opacity: 0.6;
}

button {
  padding: 10px;
  border: 1px solid #000;
  cursor: pointer;
}

button:disabled {
  cursor: not-allowed;
  opacity: 0.5;
}

.success {
  color: #87CC27;
  font-weight: bold;
}
</style>
