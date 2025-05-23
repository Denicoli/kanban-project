<template>
  <div class="flex justify-between items-center px-6 py-4 bg-gray-200 shadow-md border-b border-gray-200 sticky top-0">
    <div class="flex items-center space-x-4">
      <router-link
        v-if="showBack"
        to="/"
        class="flex items-center text-blue-600 hover:underline font-semibold"
      >
        <ChevronLeftIcon class="w-5 h-5" />
        <span class="ml-2 hidden sm:inline">Dashboard</span>
      </router-link>
      <h1 class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 text-2xl font-bold text-gray-900 ml-4">{{ title }}</h1>
    </div>
    <div class="relative">
      <button
        id="user-button"
        @click="toggleUserMenu"
        class="bg-gray-100 p-2 rounded-full hover:bg-white transition"
      >
        <UserIcon class="w-5 h-5 text-gray-600" />
      </button>
      <div
        id="user-menu"
        v-if="showUserMenu"
        class="absolute right-0 mt-2 w-48 bg-gray-50 shadow-lg rounded-lg p-4 z-10"
      >
        <div class="flex flex-col items-center text-center">
          <p class="text-sm text-gray-700">Hi, {{ user?.name }}!</p>
        </div>
        <button
          @click="logout"
          class="mt-3 w-full py-1 text-sm text-red-500 hover:underline text-center"
        >
          Logout
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '../axios'

const props = defineProps({
  title: String,
  showBack: { type: Boolean, default: false }
})

const user = ref(null)
const showUserMenu = ref(false)
const router = useRouter()

const fetchUser = async () => {
  const { data } = await api.get('/user')
  user.value = data
}

const toggleUserMenu = () => {
  showUserMenu.value = !showUserMenu.value
}

const handleClickOutside = (event) => {
  if (!event.target.closest('#user-button') && !event.target.closest('#user-menu')) {
    showUserMenu.value = false
  }
}

const logout = async () => {
  await api.post('/logout')
  router.push('/login')
}

onMounted(() => {
  fetchUser()
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>