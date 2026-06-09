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
  return name.value && email.value && password.value && confirmPassword.value && specialization.value
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

const passwordRules = computed(() => [
  { label: 'Mazais burts', valid: hasLowercase.value },
  { label: 'Lielais burts', valid: hasUppercase.value },
  { label: 'Skaitlis', valid: hasNumber.value },
  { label: 'Vismaz 8 simboli', valid: hasLength.value }
])

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
    window.dispatchEvent(new Event('authUpdated'))
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
  <main class="registration-page">
    <section class="registration-layout">
      <aside class="registration-aside" aria-label="Psihologa reģistrācijas ievads">
        <p class="eyebrow">Speciālista konts</p>
        <h1>Izveido psihologa profilu</h1>
        <p>
          Šī forma ir paredzēta psihologa piekļuvei. Pēc reģistrācijas varēsi veidot
          laikus, pārskatīt klientus un pierakstīt darba piezīmes.
        </p>
      </aside>

      <form @submit.prevent="submitForm" class="registration-card">
        <div class="form-heading">
          <h2>Psihologa reģistrācija</h2>
          <p>Aizpildi speciālista datus, kas būs redzami administrēšanas panelī.</p>
        </div>

        <div class="form-group">
          <label for="psychologist-name">Vārds</label>
          <input id="psychologist-name" v-model="name" type="text" placeholder="Vārds Uzvārds" autocomplete="name" />
        </div>

        <div class="form-group">
          <label for="psychologist-email">E-pasts</label>
          <input id="psychologist-email" v-model="email" type="email" placeholder="vards@epasts.lv" autocomplete="email" />
          <p v-if="email && !isValidEmail" class="field-note invalid">Ievadi korektu e-pasta adresi.</p>
        </div>

        <div class="form-grid">
          <div class="form-group">
            <label for="psychologist-password">Parole</label>
            <div class="password-field">
              <input id="psychologist-password" v-model="password" :type="showPassword ? 'text' : 'password'" autocomplete="new-password" />
              <button
                type="button"
                class="password-toggle"
                :aria-label="showPassword ? 'Slēpt paroli' : 'Rādīt paroli'"
                @click="showPassword = !showPassword"
              >
                {{ showPassword ? 'Slēpt' : 'Rādīt' }}
              </button>
            </div>
          </div>

          <div class="form-group">
            <label for="psychologist-confirm-password">Atkārtot paroli</label>
            <div class="password-field">
              <input id="psychologist-confirm-password" v-model="confirmPassword" :type="showPassword ? 'text' : 'password'" autocomplete="new-password" />
              <button
                type="button"
                class="password-toggle"
                :aria-label="showPassword ? 'Slēpt paroli' : 'Rādīt paroli'"
                @click="showPassword = !showPassword"
              >
                {{ showPassword ? 'Slēpt' : 'Rādīt' }}
              </button>
            </div>
            <p v-if="confirmPassword && !passwordsMatch" class="field-note invalid">Paroles nesakrīt.</p>
          </div>
        </div>

        <div class="rules" aria-label="Paroles prasības">
          <span
            v-for="rule in passwordRules"
            :key="rule.label"
            :class="['rule-pill', { valid: rule.valid }]"
          >
            {{ rule.label }}
          </span>
        </div>

        <div class="form-group">
          <label for="psychologist-specialization">Specializācija</label>
          <input
            id="psychologist-specialization"
            v-model="specialization"
            type="text"
            placeholder="Sporta psiholoģija, konsultācijas jauniešiem"
          />
        </div>

        <div class="form-group">
          <label for="psychologist-bio">Bio</label>
          <textarea
            id="psychologist-bio"
            v-model="bio"
            placeholder="Īss apraksts par pieredzi, darba pieeju vai pētījuma fokusu"
            rows="5"
          ></textarea>
        </div>

        <p v-if="errorMessage" class="status-message error">{{ errorMessage }}</p>
        <p v-if="successMessage" class="status-message success">{{ successMessage }}</p>

        <button class="submit-button" type="submit" :disabled="!isFormValid || isSubmitting">
          {{ isSubmitting ? 'Izveido...' : 'Reģistrēt psihologu' }}
        </button>
      </form>
    </section>
  </main>
</template>

<style scoped>
.registration-page {
  min-height: 100vh;
  padding: 128px 24px 56px;
  background:
    radial-gradient(circle at top right, rgba(135, 204, 39, 0.16), transparent 32%),
    linear-gradient(180deg, #f8fbff 0%, #eef7ff 100%);
}

.registration-layout {
  width: min(100%, 1040px);
  margin: 0 auto;
  display: grid;
  grid-template-columns: minmax(260px, 0.8fr) minmax(320px, 1.2fr);
  gap: 28px;
  align-items: stretch;
}

.registration-aside,
.registration-card {
  border: 1px solid rgba(34, 85, 179, 0.1);
  background: rgba(255, 255, 255, 0.94);
  box-shadow: 0 18px 45px rgba(34, 85, 179, 0.1);
}

.registration-aside {
  border-radius: 22px;
  padding: 34px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  gap: 16px;
}

.eyebrow {
  margin: 0;
  color: #dd4125;
  font-size: 0.78rem;
  font-weight: 700;
  text-transform: uppercase;
}

.registration-aside h1,
.registration-aside p,
.form-heading h2,
.form-heading p {
  margin: 0;
}

.registration-aside h1 {
  color: var(--color-primary-start);
  font-family: 'Crimson Pro', serif;
  font-size: clamp(2rem, 4vw, 3.3rem);
  line-height: 1.05;
}

.registration-aside p,
.form-heading p {
  color: #5f6d83;
  line-height: 1.65;
}

.registration-card {
  border-radius: 22px;
  padding: 30px;
  display: grid;
  gap: 18px;
}

.form-heading {
  display: grid;
  gap: 8px;
}

.form-heading h2 {
  color: #17264d;
  font-size: 1.6rem;
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 16px;
}

.form-group {
  display: grid;
  gap: 7px;
}

label {
  color: #32405c;
  font-size: 0.9rem;
  font-weight: 700;
}

input,
textarea {
  width: 100%;
  min-height: 46px;
  padding: 12px 14px;
  border: 1px solid #cbd8e6;
  border-radius: 12px;
  background: #fbfdff;
  color: #17264d;
  font: inherit;
  transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
}

textarea {
  min-height: 130px;
  resize: vertical;
}

input:focus,
textarea:focus {
  outline: none;
  border-color: var(--color-primary-start);
  background: #fff;
  box-shadow: 0 0 0 4px rgba(34, 85, 179, 0.1);
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
  min-height: 46px;
  padding: 0 14px;
  border: 1px solid rgba(34, 85, 179, 0.14);
  border-radius: 12px;
  background: #eef5ff;
  color: var(--color-primary-start);
  font-weight: 700;
  cursor: pointer;
}

.rules {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.rule-pill {
  border-radius: 999px;
  padding: 7px 11px;
  background: #eef2f7;
  color: #6d7890;
  font-size: 0.82rem;
  font-weight: 700;
}

.rule-pill.valid {
  background: rgba(135, 204, 39, 0.16);
  color: #4a7f15;
}

.field-note,
.status-message {
  margin: 0;
  font-size: 0.9rem;
  font-weight: 700;
}

.invalid {
  color: #b22f1e;
}

.status-message {
  border-radius: 12px;
  padding: 12px 14px;
}

.error {
  background: rgba(221, 65, 37, 0.1);
  color: #9f2f22;
}

.success {
  background: rgba(135, 204, 39, 0.16);
  color: #4a7f15;
}

.submit-button {
  min-height: 48px;
  border: none;
  border-radius: 999px;
  background: linear-gradient(135deg, #dd4125 0%, #b22f1e 100%);
  color: #fff;
  font: inherit;
  font-weight: 700;
  cursor: pointer;
  box-shadow: 0 14px 28px rgba(221, 65, 37, 0.24);
  transition: transform 0.2s ease, box-shadow 0.2s ease, opacity 0.2s ease;
}

.submit-button:hover:not(:disabled) {
  transform: translateY(-1px);
  box-shadow: 0 18px 34px rgba(221, 65, 37, 0.3);
}

.submit-button:disabled {
  cursor: not-allowed;
  opacity: 0.55;
  box-shadow: none;
}

@media (max-width: 820px) {
  .registration-page {
    padding: 104px 16px 40px;
  }

  .registration-layout {
    grid-template-columns: 1fr;
  }

  .registration-aside {
    padding: 26px;
  }
}

@media (max-width: 560px) {
  .registration-page {
    padding: 88px 12px 32px;
  }

  .registration-card {
    padding: 22px;
    border-radius: 16px;
  }

  .form-grid {
    grid-template-columns: 1fr;
  }

  .password-field {
    grid-template-columns: 1fr;
  }

  .password-toggle {
    width: 100%;
  }
}
</style>
