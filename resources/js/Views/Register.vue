<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="w-full max-w-md bg-white p-6 rounded shadow">
      <h2 class="text-2xl font-bold mb-4 text-center">Sign Up</h2>
      <form @submit.prevent="register">
        <input v-model="form.name" type="text" placeholder="Name" class="input" />
        <input v-model="form.email" type="email" placeholder="Email" class="input" />
        <input v-model="form.password" type="password" placeholder="Password" class="input" />
        <input v-model="form.password_confirmation" type="password" placeholder="Password confirmation" class="input" />
        <button type="submit" class="btn">Create account</button>
        <p v-if="error" class="text-red-500 mt-2">{{ error }}</p>
      </form>

      <div class="mt-4 text-sm font-normal">
        Already have an account? 
        <router-link to="/login" class="text-blue-600 hover:underline">Login here</router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue'
import api from '../axios'
import { useRouter } from 'vue-router'

const router = useRouter()
const error = ref(null)

const form = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
})

const register = async () => {
  error.value = null
  if (form.password !== form.password_confirmation) {
    error.value = 'Passwords do not match'
    return
  }
  try {
    const { data } = await api.post('/register', form)
    localStorage.setItem('token', data.token)
    router.push('/')
  } catch (err) {
    error.value = err.response?.data?.message || 'An error occurred during registration'
  }
}
</script>

<style scoped>
.input {
  @apply w-full p-2 mb-3 border rounded;
}
.btn {
  @apply w-full py-2 bg-blue-600 text-white rounded hover:bg-blue-700;
}
</style>
