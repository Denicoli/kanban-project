<template>
  <div class="p-6 max-w-md mx-auto">
    <h1 class="text-2xl font-bold mb-4">Criar conta</h1>
    <form @submit.prevent="register">
      <input v-model="form.name" type="text" placeholder="Nome" class="input" />
      <input v-model="form.email" type="email" placeholder="Email" class="input" />
      <input v-model="form.password" type="password" placeholder="Senha" class="input" />
      <input v-model="form.password_confirmation" type="password" placeholder="Confirme a senha" class="input" />
      <button type="submit" class="btn">Registrar</button>
      <p v-if="error" class="text-red-500 mt-2">{{ error }}</p>
    </form>
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
  try {
    const { data } = await api.post('/register', form)
    localStorage.setItem('token', data.token)
    router.push('/')
  } catch (err) {
    error.value = err.response?.data?.message || 'Erro no registro'
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
