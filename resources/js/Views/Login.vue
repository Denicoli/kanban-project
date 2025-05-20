<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="w-full max-w-md bg-white p-6 rounded shadow">
      <h2 class="text-2xl font-bold mb-4 text-center">Login</h2>
      
      <form @submit.prevent="handleLogin">
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">Email</label>
          <input
            v-model="form.email"
            type="email"
            class="w-full border px-3 py-2 rounded"
            required
          />
        </div>

        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">Senha</label>
          <input
            v-model="form.password"
            type="password"
            class="w-full border px-3 py-2 rounded"
            required
          />
        </div>

        <div v-if="error" class="mb-4 text-red-600 text-sm">{{ error }}</div>

        <button
          type="submit"
          class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700"
        >
          Entrar
        </button>
      </form>
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

        // Armazena o token (ex: localStorage, pinia, etc.)
        localStorage.setItem('token', response.data.token)

        // Redireciona pro dashboard
        this.$router.push('/')
      } catch (err) {
        this.error = err.response?.data?.message || 'Erro ao fazer login.'
      }
    },
  },
}
</script>
