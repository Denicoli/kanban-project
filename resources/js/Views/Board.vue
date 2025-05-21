<template>
  <div class="p-0 min-h-screen bg-gray-100">
    <AppHeader :title="board?.name || 'Board'" :showBack="true" />
    <div class="p-6">
      <div class="flex gap-4">
        <Column
          v-for="column in columns"
          :key="column.id"
          :title="column.title"
          @edit="newTitle => editColumn(column, newTitle)"
          @delete="() => deleteColumn(column)"
          @add-task="() => addTaskToColumn(column)"
        />
        <button
          @click="openCreateColumn"
          class="flex items-center gap-1 mt-2 text-blue-600 hover:text-blue-800 font-medium"
        >
          <PlusIcon class="w-5 h-5" />
          <span class="select-none">Add Column</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import api from '../axios'
import AppHeader from '@/Components/AppHeader.vue'
import Column from '@/Components/Column.vue'

const route = useRoute()
const board = ref(null)
const columns = ref([])

onMounted(async () => {
  const { id } = route.params
  const { data } = await api.get(`/boards/${id}`)
  board.value = data
  columns.value = data.columns
})
</script>