<template>
  <div class="flex flex-col items-center w-64">
    <div class="flex items-center justify-between w-full mb-2">
      <h3 class="font-semibold text-left">{{ title }}</h3>
      <div class="relative">
        <button @click="toggleMenu" class="p-1 rounded hover:bg-gray-200">
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
            <circle cx="12" cy="5" r="1.5"/>
            <circle cx="12" cy="12" r="1.5"/>
            <circle cx="12" cy="19" r="1.5"/>
          </svg>
        </button>
        <div
          v-if="showMenu"
          class="absolute right-0 mt-2 w-32 bg-white shadow-lg rounded z-10"
        >
          <button @click="openEdit" class="block w-full text-left px-4 py-2 hover:bg-gray-100">Edit</button>
          <button @click="emitDelete" class="block w-full text-left px-4 py-2 text-red-500 hover:bg-gray-100">Delete</button>
        </div>
      </div>
    </div>
    <div 
      class="bg-white shadow p-4 rounded-lg w-full flex-1 overflow-y-auto custom-scrollbar"
      style="max-height: 400px; min-height: 100px;"
    >
      <slot />
    </div>

    <button 
      @click="emitAddTask" 
      class="flex items-center gap-1 mt-2 text-blue-600 hover:text-blue-800 font-medium"
    >
      <PlusIcon class="w-5 h-5" />
      <span class="select-none">Add Task</span>
    </button>
    
    <div v-if="showEditalModal" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
      <div class="bg-white p-6 rounded shadow-lg w-80">
        <h3 class="text-lg font-semibold mb-4">Edit Title</h3>
        <input 
          v-model="editTitle" 
          type="text" 
          class="border rounded w-full p-2 mb-4" 
          placeholder="Enter new title"
          required
        />
        <button @click="showEditalModal = false" class="ml-2 text-gray-500">Cancel</button>
        <button @click="saveEdit" class="bg-blue-500 text-white px-4 py-2 rounded">Save</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref, watch } from 'vue'

const props = defineProps({
  title: String
})
const emit = defineEmits(['edit', 'delete'])

const showMenu = ref(false)
const showEditalModal = ref(false)
const editTitle = ref(props.title)

const toggleMenu = () => {
  showMenu.value = !showMenu.value
}

const openEdit = () => {
  showEditalModal.value = true
  editTitle.value = props.title
  showMenu.value = false
}

const emitEdit = () => {
  emit('edit', editTitle.value)
  showEditalModal.value = false
}

const emitDelete = () => {
  emit('delete')
  showMenu.value = false
}

const emitAddTask = () => {
  emit('add-task')
}

const handleClickOutside = (event) => {
  if (!event.target.closest('.relative')) {
    showMenu.value = false
  }
}

watch(showMenu, (newValue) => {
  if (newValue) document.addEventListener('click', handleClickOutside)
  else document.removeEventListener('click', handleClickOutside)
})
onMounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<style scoped>
  .custom-scrollbar::-webkit-scrollbar {
    width: 6px;
  }
  .custom-scrollbar::-webkit-scrollbar-thumb {
    background: #e5e7eb;
    border-radius: 4px;
  }
  .custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
  }
  .custom-scrollbar {
    scrollbar-width: thin;
    scrollbar-color: #e5e7eb transparent;
  }
</style>