<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="w-full max-w-md bg-white p-6 rounded shadow">
      <h2 class="text-2xl font-bold mb-4 text-center">Sign In</h2>
      
      <form @submit.prevent="handleLogin">
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">Email</label>
          <input v-model="form.email" type="email" class="input" required />
        </div>

        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">Password</label>
          <input v-model="form.password" type="password" class="input" required />
        </div>

        <div v-if="error" class="mb-4 text-red-600 text-sm">{{ error }}</div>

        <button
          type="submit"
          class="w-full bg-blue-600 font-semibold text-white py-2 rounded hover:bg-blue-700"
        >
          Login
        </button>
      </form>
      <div class="mt-4 text-sm font-normal">
          Don't have an account? 
          <router-link to="/register" class="text-blue-600 hover:underline">Register here</router-link>
        </div>
    </div>
  </div>
</template>

<script>
import api from '../axios'

export default {
  data() {
    return {
      form: {
        email: '',
        password: '',
      },
      error: null,
    }
  },
  methods: {
    async handleLogin() {
      this.error = null
      try {
        const response = await api.post('/login', this.form)

        localStorage.setItem('token', response.data.token)

        this.$router.push('/')
      } catch (err) {
        this.error = err.response?.data?.message || 'Login failed'
      }
    },
  },
}
</script>

<style scoped>
  .input {
  @apply w-full border px-3 py-2 rounded;
  }
  .btn {
    @apply w-full py-2 bg-blue-600 text-white rounded hover:bg-blue-700;
  }
</style>
