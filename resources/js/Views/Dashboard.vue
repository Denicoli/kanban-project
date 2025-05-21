<template>
  <div class="p-0 min-h-screen bg-gray-100">
    <AppHeader title="My Boards" />

    <div class="p-6 min-h-screen">
      <!-- Board Header -->
      <div class="flex justify-end items-end mb-4">
        <!-- <p class="text-gray-600 text-lg font-medium">Name</p>
        <p class="text-gray-600 text-lg font-medium">Description</p> -->
        <button 
          @click="openModal"
          class="flex items-center space-x-2 bg-blue-600 text-white font-semibold rounded-full px-4 py-2 transition-colors
           hover:scale-105 hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-400" 
        >
          <PlusIcon class="w-5 h-5" strokeWidth="2.5" />
          <span class="select-none">New Board</span>
        </button>
      </div>

      <!-- Board List -->
      <div v-if="boards.length" class="space-y-2">
        <div v-for="board in boards" :key="board.id" class="flex justify-between items-center bg-white shadow p-4 rounded-lg hover:shadow-md transition">
          <div class="flex-1">
            <div class="font-semibold">{{ board.name }}</div>
            <div class="text-gray-500">{{ board.description }}</div>
          </div>
          <div class="flex space-x-2">
            <button @click="goToBoard(board.id)" class="text-green-600 hover:underline">
              <EyeIcon class="w-6 h-6" />
            </button>
            <button @click="editBoard(board)" class="text-yellow-500 hover:underline">
              <PencilIcon class="w-5 h-5" />
            </button>
            <button @click="deleteBoard(board.id)" class="text-red-500 hover:underline">
              <TrashIcon class="w-5 h-5" />
            </button>
          </div>
        </div>
      </div>
      <div v-else class="text-gray-500 text-sm mt-4">No boards yet.</div>

      <BoardModal
        v-if="showModal"
        :isOpen="showModal"
        @close="showModal = false"
        @created="fetchBoards"
        :board="selectedBoard"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import api from '../axios'
import { useRouter } from 'vue-router'
import BoardModal from '@/Components/BoardModal.vue'
import AppHeader from '@/Components/AppHeader.vue'

const router = useRouter()
const user = ref(null)
const boards = ref([])
const showUserMenu = ref(false)
const showModal = ref(false)
const selectedBoard = ref(null)

const fetchUser = async () => {
  try {
    const response = await api.get('/user')
    user.value = response.data
  } catch (error) {
    console.error('Error fetching user:', error)
    router.push('/login')
  }
}

const fetchBoards = async () => {
  try {
    const { data } = await api.get('/boards')
    boards.value = data
  } catch (error) {
    console.error('Error fetching boards:', error)
  }
}

const openModal = () => {
  showModal.value = true
  selectedBoard.value = null
}

const goToBoard = (boardId) => {
  router.push(`/boards/${boardId}`)
}

const editBoard = (board) => {
  selectedBoard.value = board
  showModal.value = true
}

const deleteBoard = async (boardId) => {
  if (confirm('Are you sure you want to delete this board?')) {
    try {
      await api.delete(`/boards/${boardId}`)
      fetchBoards()
    } catch (error) {
      console.error('Failed to delete board:', error)
    }
  }
}

const toggleUserMenu = () => {
  showUserMenu.value = !showUserMenu.value
}

const logout = async () => {
  try {
    await api.post('/logout')
    localStorage.removeItem('token')
    router.push('/login')
  } catch (error) {
    console.error('Error logging out:', error)
  }
}

const handleClickOutside = (event) => {
  const userButton = document.getElementById('user-button')
  const userMenu = document.getElementById('user-menu')

  if (userMenu && !userButton.contains(event.target) && !userMenu.contains(event.target)) {
    showUserMenu.value = false
  }
}

onMounted(() => {
  fetchUser()
  fetchBoards()
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<style scoped>
</style>
