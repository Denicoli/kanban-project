<template>
  <div class="p-0 min-h-screen bg-gray-100">
    <AppHeader title="My Boards" />

    <div class="p-6 min-h-screen">
      <!-- Board Header -->
      <div class="flex justify-end items-end mb-4">
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
      <div v-if="boards.length" class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div 
          v-for="board in boards" 
          :key="board.id" 
          class="relative bg-white shadow p-4 rounded-lg hover:shadow-md transition cursor-pointer group"
          @click.stop="goToBoard(board.id)"
        >
          <div class="font-semibold text-lg mb-1 text-gray-800 truncate">{{ board.name }}</div>
          <div class="text-gray-500 text-sm mb-2 truncate">{{ board.description }}</div>
          <div class="absolute top-3 right-3 flex space-x-2 opacity-0 group-hover:opacity-100 transition">
            <button 
              @click.stop="editBoard(board)"
              class="text-yellow-500 hover:text-yellow-600 hover:bg-yellow-200 rounded-md p-1 transition"
              title="Edit"
            >
              <PencilIcon class="w-5 h-5" />
            </button>
            <button 
              @click.stop="openDeleteBoardModal(board)" 
              class="text-red-500 hover:text-red-600 hover:bg-red-200 rounded-md p-1 transition"
              title="Delete"
            >
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
      <DeleteBoardModal
        :isOpen="showDeleteBoardModal"
        :board="boardToDelete"
        @close="showDeleteBoardModal = false"
        @delete="confirmDeleteBoard"
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
import DeleteBoardModal from '@/Components/DeleteBoardModal.vue'

const router = useRouter()
const user = ref(null)
const boards = ref([])
const showUserMenu = ref(false)
const showModal = ref(false)
const selectedBoard = ref(null)
const showDeleteBoardModal = ref(false)
const boardToDelete = ref(null)

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

const openDeleteBoardModal = (board) => {
  boardToDelete.value = board
  showDeleteBoardModal.value = true
}

const confirmDeleteBoard = async () => {
  if (!boardToDelete.value) return
  try {
    await api.delete(`/boards/${boardToDelete.value.id}`)
    fetchBoards()
    showDeleteBoardModal.value = false
    boardToDelete.value = null
  } catch (error) {
    console.error('Failed to delete board: ', error)
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