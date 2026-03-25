<script setup>
import { ref, computed } from 'vue'

const name = ref('')
const surname = ref('')
const email = ref('')
const password = ref('')
const confirmPassword = ref('')
const birthdate = ref('')
const personasKods = ref('')
const talrunis = ref('')

const errorMessage = ref('')

// Email validation
const isValidEmail = computed(() => {
  const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  return regex.test(email.value)
})

// Password rules
const hasLowercase = computed(() => /[a-z]/.test(password.value))
const hasUppercase = computed(() => /[A-Z]/.test(password.value))
const hasNumber = computed(() => /[0-9]/.test(password.value))
const hasLength = computed(() => password.value.length >= 8)

const isValidPassword = computed(() => {
  return hasLowercase.value &&
         hasUppercase.value &&
         hasNumber.value &&
         hasLength.value
})

// Password match
const passwordsMatch = computed(() => {
  return password.value === confirmPassword.value && password.value !== ''
})

// Phone validation (8 digits)
const isValidPhone = computed(() => {
  return /^[0-9]{8}$/.test(talrunis.value)
})

// Personas kods validācija 
const isValidPersonasKods = computed(() => {
  const regex = /^[0-9]{6}-[0-9]{5}$/
  return regex.test(personasKods.value)
})

const formatPersonasKods = () => {
  let value = personasKods.value.replace(/\D/g, '')

  if (value.length > 6) {
    value = value.slice(0, 6) + '-' + value.slice(6, 11)
  }

  personasKods.value = value
}


// Required fields check
const allFieldsFilled = computed(() => {
  return name.value &&
         surname.value &&
         email.value &&
         password.value &&
         confirmPassword.value &&
         birthdate.value &&
         personasKods.value &&
         talrunis.value
})

// Final validation
const isFormValid = computed(() => {
  return allFieldsFilled.value &&
         isValidEmail.value &&
         isValidPassword.value &&
         passwordsMatch.value &&
         isValidPhone.value &&
         isValidPersonasKods
})

// Submit
const submitForm = () => {
  if (!isFormValid.value) {
    errorMessage.value = "Lūdzu aizpildi visus laukus pareizi!"
    return
  }

  errorMessage.value = ''
  alert('Form submitted successfully!')

  name.value = ''
  surname.value = ''
  email.value = ''
  password.value = ''
  confirmPassword.value = ''
  birthdate.value = ''
  personasKods.value = ''
  talrunis.value = ''
}
</script>

<template>
  <form @submit.prevent="submitForm" class="registration">
    <div class="form-group">
      <label>Vārds</label>
      <input v-model="name" type="text" placeholder="Vārds" />
    </div>

    <div class="form-group">
      <label>Uzvārds</label>
      <input v-model="surname" type="text" placeholder="Uzvārds" />
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
      <p :class="{ valid: hasLowercase, invalid: !hasLowercase }">
        Mazais burts
      </p>
      <p :class="{ valid: hasUppercase, invalid: !hasUppercase }">
        Lielais burts
      </p>
      <p :class="{ valid: hasNumber, invalid: !hasNumber }">
        Skaitlis
      </p>
      <p :class="{ valid: hasLength, invalid: !hasLength }">
        Vismaz 8 simboli
      </p>
    </div>

    <div class="form-group">
      <label>Atkārtot paroli</label>
      <input v-model="confirmPassword" type="password" />
    </div>

    <p v-if="confirmPassword && !passwordsMatch" class="invalid">
      Paroles nesakrīt
    </p>

    <div class="form-group">
      <label>Dzimšanas datums</label>
      <input v-model="birthdate" type="date" />
    </div>

    <div class="form-group">
      <label>Personas kods</label>
      <input v-model="personasKods" @input="formatPersonasKods" type="text"/>
      <p v-if="personasKods && !isValidPersonasKods" class="invalid">
      Personas kodam jābūt formātā XXXXXX-XXXXX
    </p>
    </div>
    
    <div class="form-group">
      <label for="">Talrunis</label>
      <input type="text" v-model="talrunis"> 
      <p v-if="errorMessage" class="error">
        {{ errorMessage }}
      </p>
    </div>

    <button type="submit" :disabled="!isFormValid">
      Submit
    </button>
  </form>
</template>

<style scoped>
.registration {
  display: flex;
  justify-content: center;
  padding-top: 6%;
}

form {
  display: flex;
  flex-direction: column;
  gap: 16px;
  width: 300px;
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
</style>