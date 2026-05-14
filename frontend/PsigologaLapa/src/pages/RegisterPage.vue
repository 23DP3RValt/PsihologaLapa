<script setup>
import { computed, ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/services/api'

const name = ref('')
const surname = ref('')
const email = ref('')
const password = ref('')
const confirmPassword = ref('')
const birthdate = ref('')
const talrunis = ref('')
const showPassword = ref(false)

const errorMessage = ref('')
const successMessage = ref('')

const isValidEmail = computed(() => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value))
const hasLowercase = computed(() => /[a-z]/.test(password.value))
const hasUppercase = computed(() => /[A-Z]/.test(password.value))
const hasNumber = computed(() => /[0-9]/.test(password.value))
const hasLength = computed(() => password.value.length >= 8)

const isValidPassword = computed(() => {
  return hasLowercase.value && hasUppercase.value && hasNumber.value && hasLength.value
})

const passwordsMatch = computed(() => {
  return password.value === confirmPassword.value && password.value !== ''
})

const isValidPhone = computed(() => /^[0-9]{8}$/.test(talrunis.value))

const allFieldsFilled = computed(() => {
  return (
    name.value &&
    surname.value &&
    email.value &&
    password.value &&
    confirmPassword.value &&
    birthdate.value &&
    talrunis.value
  )
})

const isFormValid = computed(() => {
  return (
    allFieldsFilled.value &&
    isValidEmail.value &&
    isValidPassword.value &&
    passwordsMatch.value &&
    isValidPhone.value
  )
})

const resetForm = () => {
  name.value = ''
  surname.value = ''
  email.value = ''
  password.value = ''
  confirmPassword.value = ''
  birthdate.value = ''
  talrunis.value = ''
}

const router = useRouter()

const submitForm = async () => {
  errorMessage.value = ''
  successMessage.value = ''

  if (!isFormValid.value) {
    errorMessage.value = 'Ludzu aizpildi visus laukus pareizi.'
    return
  }

  try {
    const response = await api.post('/register-user', {
      name: name.value,
      surname: surname.value,
      email: email.value,
      password: password.value,
      password_confirmation: confirmPassword.value,
      birthdate: birthdate.value,
      talrunis: talrunis.value
    })

    const authPayload = {
      ...response,
      token: response.token
    }

    localStorage.setItem('authUser', JSON.stringify(authPayload))
    window.dispatchEvent(new Event('authUpdated'))
    successMessage.value = 'Klienta profils veiksmigi saglabats.'
    resetForm()
    router.push('/dashboard')
  } catch (error) {
    if (error.response) {
      errorMessage.value = error.response.data.message || 'Neizdevas saglabat datus.'
      return
    }

    errorMessage.value = 'Savienojuma kluda.'
  }
}
</script>

<template>
  <form @submit.prevent="submitForm" class="registration">
    <div class="intro">
      <h1>Klienta registracija</h1>
      <p>Si forma ir paredzeta tikai klientiem. Psihologa konts sistema ir viens.</p>
    </div>

    <div class="form-group">
      <label>Vards</label>
      <input v-model="name" type="text" placeholder="Vards" />
    </div>

    <div class="form-group">
      <label>Uzvards</label>
      <input v-model="surname" type="text" placeholder="Uzvards" />
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
      <label>Atkartot paroli</label>
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

    <p v-if="confirmPassword && !passwordsMatch" class="invalid">Paroles nesakrit.</p>

    <div class="form-group">
      <label>Dzimsanas datums</label>
      <input v-model="birthdate" type="date" />
    </div>

    <div class="form-group">
      <label>Talrunis</label>
      <input v-model="talrunis" type="text" />
    </div>

    <p v-if="errorMessage" class="error">{{ errorMessage }}</p>
    <p v-if="successMessage" class="success">{{ successMessage }}</p>

    <button type="submit" :disabled="!isFormValid">Registreties</button>
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

input {
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

.error {
  color: red;
  font-weight: bold;
}

.success {
  color: #87CC27;
  font-weight: bold;
}
</style>
