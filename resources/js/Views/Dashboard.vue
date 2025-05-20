<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Dashboard</h1>
    <p>Bem-vindo, {{ user?.name }}</p>
    <button @click="logout" class="btn mt-4">Logout</button>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '../axios'
import { useRouter } from 'vue-router'

const user = ref(null)
const router = useRouter()

onMounted(async () => {
  try {
    const response = await api.get('/user', {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`,
      },
    })
    user.value = response.data
  } catch {
    router.push('/login')
  }
})

const logout = async () => {
  await api.post('/logout', {}, {
    headers: {
      Authorization: `Bearer ${localStorage.getItem('token')}`,
    },
  })
  localStorage.removeItem('token')
  router.push('/login')
}
</script>

<style scoped>
.btn {
  @apply px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600;
}
</style>
