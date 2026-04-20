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
      <input v-model="password" type="password" />
    </div>

    <div class="rules">
      <p :class="{ valid: hasLowercase, invalid: !hasLowercase }">Mazais burts</p>
      <p :class="{ valid: hasUppercase, invalid: !hasUppercase }">Lielais burts</p>
      <p :class="{ valid: hasNumber, invalid: !hasNumber }">Skaitlis</p>
      <p :class="{ valid: hasLength, invalid: !hasLength }">Vismaz 8 simboli</p>
    </div>

    <div class="form-group">
      <label>Atkārtot paroli</label>
      <input v-model="confirmPassword" type="password" />
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
