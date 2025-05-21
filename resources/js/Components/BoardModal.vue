<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div 
      class="bg-white rounded-2xl p-8 w-full max-w-md shadow-2xl ring-1 ring-black ring-opacity-5"
      @click.stop
    >
      <h2 class="text-xl font-extrabold mb-6 text-gray-900">{{ board ? 'Edit Board' : 'Create New Board' }}</h2>
      <BoardForm
        :name="board?.name || ''"
        :description="board?.description || ''"
        @submit="createOrUpdateBoard"
        @cancel="close"
      />
    </div>
  </div>
</template>

<script setup>
import api from '../axios'
import BoardForm from './BoardForm.vue'

const props = defineProps({
  isOpen: Boolean,
  board: Object,
})

const emit = defineEmits(['close', 'created'])

const createOrUpdateBoard = async (formData) => {
  if (!formData.name.trim()) return

  try {
    if (props.board) {
      // Update existing board
      const response = await api.put(`/boards/${props.board.id}`, {
        name: formData.name,
        description: formData.description,
      })
      if (response.status === 200) {
        emit('created', response.data)
      }
      close()
      return
    }
    // Create new board
    const response = await api.post('/boards', {
      name: formData.name.trim(),
      description: formData.description.trim(),
    })
    if (response.status === 201) {
      emit('created', response.data)
    }
    close()
  } catch (error) {
    console.error('Failed to create or update board:', error)
  }
}

const close = () => {
  emit('close')
}
</script>